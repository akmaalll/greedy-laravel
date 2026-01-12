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
        
        if (!$tanggalString || !$mulaiString) {
            return false;
        }

        if (!$end) {
             $end = Carbon::parse($mulaiString)->addHour()->toTimeString();
        }
        $selesaiString = $end instanceof Carbon ? $end->format('H:i:s') : $end;

        // 1. Enforce working hours (08:00 - 17:00)
        if ($mulaiString < self::WORKING_HOUR_START || $selesaiString > self::WORKING_HOUR_END) {
            return false;
        }

        return User::whereRole('photographer')
            ->whereDoesntHave('ketersediaan', function($query) use ($tanggalString, $mulaiString, $selesaiString) {
                // Conflict with manual "tidak_tersedia" blocks
                $query->whereDate('tanggal', $tanggalString)
                      ->where('status', 'tidak_tersedia')
                      ->where('waktu_mulai', '<', $selesaiString)
                      ->where('waktu_selesai', '>', $mulaiString);
            })
            ->whereDoesntHave('penugasan', function($query) use ($tanggalString, $mulaiString, $selesaiString) {
                // Conflict with existing active assignments (not cancelled or rejected)
                $query->whereHas('pesanan', function($q) use ($tanggalString, $mulaiString, $selesaiString) {
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

        if (!$tanggalString || !$mulaiString) {
            return null;
        }
        $selesai = $pesanan->waktu_selesai_acara;
        if (!$selesai) {
            $selesai = Carbon::parse($mulaiString)->addHour()->toTimeString();
        }
        $selesaiString = $selesai instanceof Carbon ? $selesai->format('H:i:s') : $selesai;

        // 1. Get available photographers using dynamic conflict detection
        $availablePhotographers = User::whereRole('photographer')
            ->whereDoesntHave('ketersediaan', function($query) use ($tanggalString, $mulaiString, $selesaiString) {
                $query->whereDate('tanggal', $tanggalString)
                      ->where('status', 'tidak_tersedia')
                      ->where('waktu_mulai', '<', $selesaiString)
                      ->where('waktu_selesai', '>', $mulaiString);
            })
            ->whereDoesntHave('penugasan', function($query) use ($tanggalString, $mulaiString, $selesaiString) {
                $query->whereHas('pesanan', function($q) use ($tanggalString, $mulaiString, $selesaiString) {
                    $q->whereDate('tanggal_acara', $tanggalString)
                      ->whereIn('status', ['menunggu', 'dikonfirmasi', 'berlangsung'])
                      ->where('waktu_mulai_acara', '<', $selesaiString)
                      ->where(DB::raw('COALESCE(waktu_selesai_acara, DATE_ADD(waktu_mulai_acara, INTERVAL 1 HOUR))'), '>', $mulaiString);
                })
                ->whereIn('status', ['ditugaskan', 'diterima']);
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
        
        return $this->penugasanRepository->create([
            'pesanan_id' => $pesanan->id,
            'fotografer_id' => $selected->id,
            'status' => 'diterima',
            'catatan' => "Auto-assigned (Greedy). Variables: Rating: $rating, Workload Today: $workload tasks.",
        ]);
    }
}
