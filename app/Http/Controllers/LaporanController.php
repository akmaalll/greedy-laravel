<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Pesanan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

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

        $data = Pembayaran::whereBetween('waktu_bayar', [$start, $end])
            ->where('status', 'lunas')
            ->with('pesanan.klien')
            ->get();

        $totalPendapatan = $data->sum('jumlah');

        return view('pages.laporan.pendapatan', compact('data', 'start', 'end', 'totalPendapatan'));
    }

    public function fotografer()
    {
        $data = User::where('role_id', 4)
            ->withCount('penugasan')
            ->withAvg('ratingDiterima', 'rating')
            ->get();

        return view('pages.laporan.fotografer', compact('data'));
    }
}
