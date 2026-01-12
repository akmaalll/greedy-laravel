<?php

namespace App\Services;

use App\Repositories\PenugasanFotograferRepository;
use App\Models\User;
use App\Models\KetersediaanFotografer;
use App\Models\Pesanan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SchedulingService
{
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
        
        if (!$tanggalString || !$mulaiString) {
            return false;
        }
        if (!$end) {
             $end = Carbon::parse($mulaiString)->addHour()->toTimeString();
        }
        $selesaiString = $end instanceof Carbon ? $end->format('H:i:s') : $end;

        return User::whereRole('photographer')
            ->whereHas('ketersediaan', function($query) use ($tanggalString, $mulaiString, $selesaiString) {
                $query->whereDate('tanggal', $tanggalString)
                      ->where('status', 'tersedia')
                      ->where('waktu_mulai', '<=', $mulaiString)
                      ->where('waktu_selesai', '>=', $selesaiString);
            })
            ->exists();
    }

    /**
     * Greedy Algorithm to automatically assign photographer
     * Criteria:
     * 1. Availability (must be 'tersedia' on that date/time)
     * 2. Performance/Rating (higher rating preferred)
     * 3. Workload (least busy preferred among same availability)
     */
    public function assignPhotographer(Pesanan $pesanan)
    {
        $tanggal = $pesanan->tanggal_acara;
        $mulai = $pesanan->waktu_mulai_acara;

        $tanggalString = $tanggal ? ($tanggal instanceof Carbon ? $tanggal->format('Y-m-d') : $tanggal) : null;
        $mulaiString = $mulai ? ($mulai instanceof Carbon ? $mulai->format('H:i:s') : $mulai) : null;

        if (!$tanggalString || !$mulaiString) {
            return null;
        }
        $selesai = $pesanan->waktu_selesai_acara;
        if (!$selesai) {
            $selesai = Carbon::parse($mulaiString)->addHour()->toTimeString();
        }
        $selesaiString = $selesai instanceof Carbon ? $selesai->format('H:i:s') : $selesai;

        // 1. Get all photographers who are available on this date and time range
        $availablePhotographers = User::whereRole('photographer')
            ->whereHas('ketersediaan', function($query) use ($tanggalString, $mulaiString, $selesaiString) {
                $query->whereDate('tanggal', $tanggalString)
                      ->where('status', 'tersedia')
                      ->where('waktu_mulai', '<=', $mulaiString)
                      ->where('waktu_selesai', '>=', $selesaiString);
            })
            ->withCount(['penugasan' => function($query) use ($tanggalString) {
                $query->whereHas('pesanan', function($q) use ($tanggalString) {
                    $q->whereDate('tanggal_acara', $tanggalString);
                });
            }])
            ->withAvg('ratingDiterima', 'rating')
            ->get();

        \Log::info('Greedy Debug:', [
            'pesanan_id' => $pesanan->id,
            'tanggal' => $tanggalString,
            'mulai' => $mulaiString,
            'selesai' => $selesaiString,
            'available_count' => $availablePhotographers->count(),
            'photographers' => $availablePhotographers->pluck('name')->toArray()
        ]);

        if ($availablePhotographers->isEmpty()) {
            return null; // Handle manual assignment if greedy fails
        }

        // 2. Sort by Rating (Desc) then Workload (Asc) - The Greedy Choice
        $selected = $availablePhotographers
            ->sortByDesc('rating_diterima_avg_rating')
            ->sortBy('penugasan_count')
            ->first();

        // 3. Update photographer availability to 'dipesan'
        // We only mark the record that covers this time slot
        KetersediaanFotografer::where('fotografer_id', $selected->id)
            ->where('tanggal', $tanggalString)
            ->where('status', 'tersedia')
            ->where('waktu_mulai', '<=', $mulaiString)
            ->where('waktu_selesai', '>=', $selesaiString)
            ->update(['status' => 'dipesan']);

        // 4. Create assignment with detailed variables for visibility
        $rating = number_format($selected->rating_diterima_avg_rating ?? 0, 1);
        $workload = $selected->penugasan_count;
        
        return $this->penugasanRepository->create([
            'pesanan_id' => $pesanan->id,
            'fotografer_id' => $selected->id,
            'status' => 'diterima',
            'catatan' => "Auto-assigned (Greedy). Variables: Rating: $rating, Workload Today: $workload tasks.",
        ]);
    }
}
