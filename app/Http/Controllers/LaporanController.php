<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Models\Pembayaran;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LaporanController extends Controller
{
    public function pesanan(Request $request)
    {
        $start = $request->start_date ?? Carbon::now()->startOfMonth()->format('Y-m-d');
        $end = $request->end_date ?? Carbon::now()->format('Y-m-d');

        $data = Pesanan::whereBetween('tanggal_acara', [$start, $end])
            ->with(['klien', 'itemPesanan.paketLayanan'])
            ->get();

        $statistics = [
            'total' => $data->count(),
            'selesai' => $data->where('status', 'selesai')->count(),
            'total_nilai' => $data->sum('total_harga'),
        ];

        return view('pages.laporan.pesanan', compact('data', 'start', 'end', 'statistics'));
    }

    public function pendapatan(Request $request)
    {
        $start = $request->start_date ?? Carbon::now()->startOfMonth()->format('Y-m-d');
        $end = $request->end_date ?? Carbon::now()->format('Y-m-d');

        $data = Pembayaran::whereBetween('tanggal_bayar', [$start, $end])
            ->where('status_pembayaran', 'lunas')
            ->with('pesanan.klien')
            ->get();

        $totalPendapatan = $data->sum('jumlah_bayar');

        return view('pages.laporan.pendapatan', compact('data', 'start', 'end', 'totalPendapatan'));
    }

    public function fotografer()
    {
        $data = User::role('photographer')
            ->withCount('penugasan')
            ->withAvg('ratingDiterima', 'rating')
            ->get();

        return view('pages.laporan.fotografer', compact('data'));
    }
}
