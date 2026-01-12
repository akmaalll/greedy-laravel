<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Models\User;
use App\Models\Pembayaran;
use App\Models\PenugasanFotografer;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the dashboard analytics page
     */
    public function index()
    {
        $user = auth()->user();
        $role = $user->role->slug;

        if ($role === 'admin') {
            return $this->adminDashboard();
        } elseif ($role === 'photographer') {
            return $this->photographerDashboard();
        } else {
            return $this->clientDashboard();
        }
    }

    protected function adminDashboard()
    {
        $data = [
            'total_orders' => Pesanan::count(),
            'total_revenue' => Pembayaran::where('status', 'lunas')->sum('jumlah'),
            'active_photographers' => User::whereRole('photographer')->count(),
            'pending_orders' => Pesanan::where('status', 'menunggu')->count(),
            'recent_orders' => Pesanan::with('klien')->latest()->take(5)->get(),
            'monthly_earnings' => Pembayaran::where('status', 'lunas')
                ->where('waktu_bayar', '>=', Carbon::now()->startOfYear())
                ->select(DB::raw('MONTH(waktu_bayar) as month'), DB::raw('SUM(jumlah) as total'))
                ->groupBy('month')
                ->get()
        ];

        return view('pages.dashboard.admin', $data);
    }

    protected function photographerDashboard()
    {
        $user = auth()->user();
        $today = Carbon::today()->toDateString();

        $data = [
            'tasks_today' => PenugasanFotografer::where('fotografer_id', $user->id)
                ->whereHas('pesanan', function($q) use ($today) {
                    $q->whereDate('tanggal_acara', $today);
                })->count(),
            'completed_tasks' => PenugasanFotografer::where('fotografer_id', $user->id)
                ->where('status', 'selesai')->count(),
            'avg_rating' => $user->ratingDiterima()->avg('rating') ?? 0,
            'recent_jobs' => PenugasanFotografer::with('pesanan.klien')
                ->where('fotografer_id', $user->id)
                ->latest()
                ->take(5)
                ->get()
        ];

        return view('pages.dashboard.photographer', $data);
    }

    protected function clientDashboard()
    {
        $user = auth()->user();

        $data = [
            'my_orders_count' => Pesanan::where('klien_id', $user->id)->count(),
            'total_spending' => Pesanan::where('klien_id', $user->id)
                ->whereIn('status', ['dikonfirmasi', 'berlangsung', 'selesai'])
                ->sum('total_harga'),
            'active_orders' => Pesanan::where('klien_id', $user->id)
                ->whereIn('status', ['menunggu', 'dikonfirmasi', 'berlangsung'])
                ->latest()
                ->take(5)
                ->get(),
            'recent_history' => Pesanan::where('klien_id', $user->id)
                ->latest()
                ->take(5)
                ->get()
        ];

        return view('pages.dashboard.client', $data);
    }
}
