<?php

namespace App\Services;

use App\Models\KetersediaanFotografer;
use App\Models\Pesanan;
use App\Models\User;
use App\Repositories\PenugasanFotograferRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SchedulingService
{
    const WORKING_HOUR_START = '08:00:00';

    const WORKING_HOUR_END = '17:00:00';

    public function __construct(
        protected PenugasanFotograferRepository $penugasanRepository
    ) {}

    /**
     * Check if any photographer is available for the given timeframe.
     */
    public function checkAvailability($date, $start, $end = null)
    {
        $tanggalString = $date ? ($date instanceof Carbon ? $date->format('Y-m-d') : $date) : null;
        $mulaiString = $start ? ($start instanceof Carbon ? $start->format('H:i:s') : $start) : null;

        if (! $tanggalString || ! $mulaiString) {
            return false;
        }

        if (! $end) {
            $end = Carbon::parse($mulaiString)->addHour()->toTimeString();
        }
        $selesaiString = $end instanceof Carbon ? $end->format('H:i:s') : $end;

        // 1. Enforce working hours (08:00 - 17:00)
        if ($mulaiString < self::WORKING_HOUR_START || $selesaiString > self::WORKING_HOUR_END) {
            return false;
        }

        return User::where('role_id', 4)
            ->whereDoesntHave('ketersediaan', function ($query) use ($tanggalString, $mulaiString, $selesaiString) {
                // Conflict with manual "tidak_tersedia" blocks
                $query->whereDate('tanggal', $tanggalString)
                    ->where('status', 'tidak_tersedia')
                    ->where('waktu_mulai', '<', $selesaiString)
                    ->where('waktu_selesai', '>', $mulaiString);
            })
            ->whereDoesntHave('penugasan', function ($query) use ($tanggalString, $mulaiString, $selesaiString) {
                // Conflict with existing active assignments (not cancelled or rejected)
                $query->whereHas('pesanan', function ($q) use ($tanggalString, $mulaiString, $selesaiString) {
                    $q->whereDate('tanggal_acara', $tanggalString)
                        ->whereIn('status', ['menunggu', 'dikonfirmasi', 'berlangsung'])
                        ->where('waktu_mulai_acara', '<', $selesaiString)
                        ->where(DB::raw('COALESCE(waktu_selesai_acara, DATE_ADD(waktu_mulai_acara, INTERVAL 1 HOUR))'), '>', $mulaiString);
                })
                    ->whereIn('status', ['ditugaskan', 'diterima']);
            })
            ->exists();
    }

    /**
     * Greedy Algorithm to automatically assign photographer
     */
    public function assignPhotographer(Pesanan $pesanan)
    {
        $tanggal = $pesanan->tanggal_acara;
        $mulai = $pesanan->waktu_mulai_acara;

        $tanggalString = $tanggal ? ($tanggal instanceof Carbon ? $tanggal->format('Y-m-d') : $tanggal) : null;
        $mulaiString = $mulai ? ($mulai instanceof Carbon ? $mulai->format('H:i:s') : $mulai) : null;

        if (! $tanggalString || ! $mulaiString) {
            return null;
        }
        $selesai = $pesanan->waktu_selesai_acara;
        if (! $selesai) {
            $selesai = Carbon::parse($mulaiString)->addHour()->toTimeString();
        }
        $selesaiString = $selesai instanceof Carbon ? $selesai->format('H:i:s') : $selesai;

        // 1. Get available photographers using dynamic conflict detection
        $availablePhotographers = User::where('role_id', 4)
            ->whereDoesntHave('ketersediaan', function ($query) use ($tanggalString, $mulaiString, $selesaiString) {
                $query->whereDate('tanggal', $tanggalString)
                    ->where('status', 'tidak_tersedia')
                    ->where('waktu_mulai', '<', $selesaiString)
                    ->where('waktu_selesai', '>', $mulaiString);
            })
            ->whereDoesntHave('penugasan', function ($query) use ($tanggalString, $mulaiString, $selesaiString) {
                $query->whereHas('pesanan', function ($q) use ($tanggalString, $mulaiString, $selesaiString) {
                    $q->whereDate('tanggal_acara', $tanggalString)
                        ->whereIn('status', ['menunggu', 'dikonfirmasi', 'berlangsung'])
                        ->where('waktu_mulai_acara', '<', $selesaiString)
                        ->where(DB::raw('COALESCE(waktu_selesai_acara, DATE_ADD(waktu_mulai_acara, INTERVAL 1 HOUR))'), '>', $mulaiString);
                })
                    ->whereIn('status', ['ditugaskan', 'diterima']);
            })
            ->withCount(['penugasan' => function ($query) use ($tanggalString) {
                $query->whereHas('pesanan', function ($q) use ($tanggalString) {
                    $q->whereDate('tanggal_acara', $tanggalString);
                });
            }])
            ->withAvg('ratingDiterima', 'rating')
            ->get();

        \Log::info('Greedy Debug:', [
            'pesanan_id' => $pesanan->id,
            'available_count' => $availablePhotographers->count(),
        ]);

        if ($availablePhotographers->isEmpty()) {
            return null;
        }

        // 2. Greedy Choice: Best Rating, then Lowest Workload
        $selected = $availablePhotographers
            ->sortByDesc('rating_diterima_avg_rating')
            ->sortBy('penugasan_count')
            ->first();

        // 3. Create assignment
        $rating = number_format($selected->rating_diterima_avg_rating ?? 0, 1);
        $workload = $selected->penugasan_count;

        $assignment = $this->penugasanRepository->create([
            'pesanan_id' => $pesanan->id,
            'fotografer_id' => $selected->id,
            'status' => 'diterima',
            'catatan' => "Auto-assigned (Greedy). Variables: Rating: $rating, Workload Today: $workload tasks.",
        ]);

        // Update ketersediaan status to 'dipesan' for visual feedback in the table
        KetersediaanFotografer::where('fotografer_id', $selected->id)
            ->whereDate('tanggal', $tanggalString)
            ->where('status', 'tersedia')
            ->update(['status' => 'dipesan']);

        return $assignment;
    }

    /**
     * Generate monthly availability for photographers.
     * Balanced hours means we distribute the days among photographers.
     */
    public function generateMonthlyAvailability(int $year, int $month, ?int $fotograferId = null)
    {
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        DB::beginTransaction();
        try {
            if ($fotograferId) {
                // Generate for a specific photographer (e.g., they want to mark themselves available)
                $photographers = User::where('id', $fotograferId)->get();
            } else {
                // Balanced - get all photographers
                $photographers = User::where('role_id', 4)->get();
            }

            if ($photographers->isEmpty()) {
                throw new \Exception('Tidak ada fotografer ditemukan.');
            }

            $currentDate = $startDate->copy();
            $photographerIndex = 0;

            while ($currentDate <= $endDate) {
                // Only generate for weekdays (Mon-Sat)
                if ($currentDate->dayOfWeek !== Carbon::SUNDAY) {

                    // If specific photographer, just add for them
                    // If balanced, pick one photographer for this day
                    $selectedPhotographer = $fotograferId ? $photographers->first() : $photographers[$photographerIndex % $photographers->count()];

                    // Check if already exists to avoid duplicates
                    $exists = KetersediaanFotografer::where('fotografer_id', $selectedPhotographer->id)
                        ->whereDate('tanggal', $currentDate->format('Y-m-d'))
                        ->exists();

                    if (! $exists) {
                        KetersediaanFotografer::create([
                            'fotografer_id' => $selectedPhotographer->id,
                            'tanggal' => $currentDate->format('Y-m-d'),
                            'waktu_mulai' => self::WORKING_HOUR_START,
                            'waktu_selesai' => self::WORKING_HOUR_END,
                            'status' => 'tersedia',
                            'catatan' => 'Otomatis dari sistem',
                        ]);
                    }

                    // Increment index only if we are balancing
                    if (! $fotograferId) {
                        $photographerIndex++;
                    }
                }
                $currentDate->addDay();
            }

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error generating availability: '.$e->getMessage());
            throw $e;
        }
    }

    /**
     * Generate availability for date range.
     * Creates records for ALL photographers on each day (not balanced).
     */
    public function generateRangeAvailability(string $startDate, string $endDate, ?int $fotograferId = null)
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        DB::beginTransaction();
        try {
            if ($fotograferId) {
                // Generate for a specific photographer
                $photographers = User::where('id', $fotograferId)->get();
            } else {
                // Generate for ALL photographers
                $photographers = User::where('role_id', 4)->get();
            }

            if ($photographers->isEmpty()) {
                throw new \Exception('Tidak ada fotografer ditemukan.');
            }

            $currentDate = $start->copy();
            $createdCount = 0;

            while ($currentDate <= $end) {
                // Only generate for weekdays (Mon-Sat)
                if ($currentDate->dayOfWeek !== Carbon::SUNDAY) {

                    // Create for ALL photographers on this day
                    foreach ($photographers as $photographer) {
                        // Check if already exists to avoid duplicates
                        $exists = KetersediaanFotografer::where('fotografer_id', $photographer->id)
                            ->whereDate('tanggal', $currentDate->format('Y-m-d'))
                            ->exists();

                        if (! $exists) {
                            KetersediaanFotografer::create([
                                'fotografer_id' => $photographer->id,
                                'tanggal' => $currentDate->format('Y-m-d'),
                                'waktu_mulai' => self::WORKING_HOUR_START,
                                'waktu_selesai' => self::WORKING_HOUR_END,
                                'status' => 'tersedia',
                                'catatan' => 'Otomatis dari sistem',
                            ]);
                            $createdCount++;
                        }
                    }
                }
                $currentDate->addDay();
            }

            DB::commit();

            \Log::info("Generated $createdCount availability records from $startDate to $endDate");

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error generating range availability: '.$e->getMessage());
            throw $e;
        }
    }
}
