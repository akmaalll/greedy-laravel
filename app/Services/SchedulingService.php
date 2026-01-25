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

        return [
            'assignment' => $assignment,
            'selected_photographer' => $selected->name,
            'candidates' => $availablePhotographers->map(function ($p) {
                $rating = $p->rating_diterima_avg_rating ?? 0;
                $workload = $p->penugasan_count;
                // Formula: Rating - (Workload * 2). 
                // Kita kalikan beban dengan 2 agar beban kerja memiliki pengaruh signifikan.
                $score = number_format($rating - ($workload * 2), 2);
                
                return [
                    'name' => $p->name,
                    'rating' => number_format($rating, 1),
                    'workload' => $workload,
                    'score' => $score,
                ];
            })->sortByDesc('score')->values()->toArray(),
        ];
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
                // Generate for everyday including Sunday
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
                // Generate for everyday including Sunday
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

    /**
     * Update real-time status of orders and photographer availability.
     * This should be called by scheduler or manually.
     */
    public function updateRealTimeStatus()
    {
        $now = Carbon::now();
        $today = $now->toDateString();
        $currentTime = $now->toTimeString();
        $logs = [];

        // 0. AUTO-START Orders (Dikonfirmasi -> Berlangsung)
        // If start time has passed but end time hasn't, mark as ongoing.
        $startingOrders = Pesanan::where('status', 'dikonfirmasi')
            ->whereDate('tanggal_acara', $today)
            ->whereTime('waktu_mulai_acara', '<=', $currentTime)
            ->whereTime('waktu_selesai_acara', '>', $currentTime)
            ->get();

        foreach ($startingOrders as $order) {
            $order->update(['status' => 'berlangsung']);
            $logs[] = "🚀 Order #{$order->nomor_pesanan} marked as BERLANGSUNG (Started).";
        }

        // 1. AUTO-COMPLETE Finished Orders (Dikonfirmasi/Berlangsung -> Selesai)
        $finishedOrders = Pesanan::whereIn('status', ['dikonfirmasi', 'berlangsung'])
            ->whereDate('tanggal_acara', $today)
            ->whereTime('waktu_selesai_acara', '<=', $currentTime) // Changed to <= to be precise
            ->get();

        foreach ($finishedOrders as $order) {
            DB::transaction(function () use ($order, &$logs) {
                // Update Order Status
                $order->update(['status' => 'selesai']);
                $logs[] = "✅ Order #{$order->nomor_pesanan} marked as SELESAI (Time finished).";

                // Update Assignment Status
                foreach ($order->penugasanFotografer as $tugas) {
                    $tugas->update([
                        'status' => 'selesai',
                        'waktu_selesai' => Carbon::now() // Record actual finish time
                    ]);
                    $logs[] = "   -> Photographer Assignment for {$tugas->fotografer->name} marked as SELESAI.";
                }
            });
        }

        // 2. Update Availability based on Real-Time Status
        $photographers = User::where('role_id', 4)->get();

        foreach ($photographers as $p) {
            // Get availability for today
            $avail = KetersediaanFotografer::where('fotografer_id', $p->id)
                ->whereDate('tanggal', $today)
                ->first();

            if (!$avail) {
                continue;
            }

            // Skip if manually set to 'tidak_tersedia' (Closed/Off)
            if ($avail->status == 'tidak_tersedia') {
                continue;
            }

            // Check if currently busy (Active Order running NOW)
            // Active means: 'dikonfirmasi' or 'berlangsung', AND time is within range
            $isBusy = \App\Models\PenugasanFotografer::where('fotografer_id', $p->id)
                ->whereHas('pesanan', function ($q) use ($today, $currentTime) {
                    $q->whereDate('tanggal_acara', $today)
                        ->whereTime('waktu_mulai_acara', '<=', $currentTime)
                        ->whereTime('waktu_selesai_acara', '>', $currentTime) // Strict check: ongoing only
                        ->whereIn('status', ['dikonfirmasi', 'berlangsung']);
                })
                ->whereNotIn('status', ['selesai', 'ditolak', 'dibatalkan']) // Double check assignment status
                ->exists();

            if ($isBusy) {
                if ($avail->status !== 'dipesan') {
                    $avail->update(['status' => 'dipesan']);
                    $logs[] = "LOG: Photographer {$p->name} is BUSY. Status updated to 'dipesan'.";
                }
            } else {
                // If not busy now, mark as available
                if ($avail->status !== 'tersedia') {
                    $avail->update(['status' => 'tersedia']);
                    $logs[] = "LOG: Photographer {$p->name} is FREE. Status updated to 'tersedia'.";
                }
            }
        }

        return $logs;
    }
}
