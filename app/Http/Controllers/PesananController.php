<?php

namespace App\Http\Controllers;

use App\Http\Requests\PesananRequest;
use App\Models\PaketLayanan;
use App\Services\PaketLayananService;
use App\Services\PesananService;
use Illuminate\Http\Request;

class PesananController extends Controller
{
    public function __construct(
        protected PesananService $service,
        protected PaketLayananService $paketService,
        protected \App\Services\SchedulingService $schedulingService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = $this->service->all();
        $paketLayanan = null;

        // If client, only show their orders and fetch available packages
        if (auth()->user()->role_id == 3) {
            $data = $data->where('klien_id', auth()->id());
            $paketLayanan = PaketLayanan::with('layanan.kategori')
                ->where('is_aktif', true)
                ->get()
                ->sortBy(function ($item) {
                    return $item->layanan->kategori->nama ?? 'Z Lainnya';
                });
        }

        $categories = \App\Models\Kategori::where('is_aktif', true)->get();

        return view('pages.pesanan.index', compact('data', 'paketLayanan', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $paketLayanan = PaketLayanan::with('layanan.kategori')
            ->where('is_aktif', true)
            ->get()
            ->sortBy(function ($item) {
                return $item->layanan->kategori->nama ?? 'Z Lainnya';
            });
        $categories = \App\Models\Kategori::where('is_aktif', true)->get();
        $selectedPaketId = $request->query('selected_paket');

        return view('pages.pesanan.create', compact('paketLayanan', 'selectedPaketId', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PesananRequest $request)
    {
        $data = $request->validated();

        // 1. Ambil paket pertama untuk hitung durasi (asumsi 1 pesanan 1 durasi paket utama)
        $firstItem = $request->items[0] ?? null;
        if ($firstItem) {
            $paket = PaketLayanan::find($firstItem['paket_layanan_id']);
            $mulai = \Carbon\Carbon::parse($data['tanggal_acara'].' '.$data['waktu_mulai_acara']);

            if ($paket->tipe_durasi == 'jam') {
                $selesai = $mulai->copy()->addHours($paket->nilai_durasi);
            } else {
                $selesai = $mulai->copy()->addMinutes($paket->nilai_durasi);
            }

            $data['waktu_selesai_acara'] = $selesai->format('H:i:s');
        }

        // 2. Check availability strictly before booking
        $isAvailable = $this->schedulingService->checkAvailability(
            $data['tanggal_acara'],
            $data['waktu_mulai_acara'],
            $data['waktu_selesai_acara'] ?? null
        );

        if (! $isAvailable) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Maaf, tidak ada fotografer tersedia pada tanggal dan jam tersebut. Silakan pilih waktu lain.');
        }

        $data['klien_id'] = auth()->id();
        $data['status'] = 'menunggu';

        $items = [];
        foreach ($request->items as $item) {
            $paket = PaketLayanan::find($item['paket_layanan_id']);
            $items[] = [
                'paket_layanan_id' => $paket->id,
                'jumlah' => $item['jumlah'],
                'harga_satuan' => $paket->harga_dasar,
                'subtotal' => $paket->harga_dasar * $item['jumlah'],
            ];
        }

        $this->service->createPesanan($data, $items);

        return redirect()->route('pesanan.index')
            ->with('success', 'Pesanan berhasil dibuat! Menunggu konfirmasi admin.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $data = $this->service->find($id);

        // Memastikan relasi penugasan dan lainnya ter-load
        $data->load(['itemPesanan.paketLayanan.layanan', 'penugasanFotografer.fotografer', 'klien', 'pembayaran']);

        return view('pages.pesanan.show', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $data = $this->service->find($id);
        $paketLayanan = $this->paketService->all();

        return view('pages.pesanan.edit', compact('data', 'paketLayanan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PesananRequest $request, $id)
    {
        $pesanan = $this->service->find($id);
        $oldStatus = $pesanan->status;

        $data = $request->validated();
        $this->service->update($id, $data);
        $pesanan->refresh(); // Refresh to get updated status

        // 1. Trigger Greedy Assignment if status changed to 'dikonfirmasi'
        if ($pesanan->status == 'dikonfirmasi') {
            if ($pesanan->penugasanFotografer->isEmpty()) {
                $result = $this->schedulingService->assignPhotographer($pesanan);

                if ($result) {
                    session()->flash('greedy_calculation', [
                        'selected' => $result['selected_photographer'],
                        'candidates' => $result['candidates'],
                    ]);
                }

                if (! $result && $oldStatus != 'dikonfirmasi') {
                    return redirect()->back()
                        ->with('warning', 'Pesanan dikonfirmasi, namun tidak ada fotografer tersedia secara otomatis. Silakan tugaskan secara manual.');
                }
            }
        }

        // 2. REVERT availability if status changed to 'selesai' or 'dibatalkan'
        if (in_array($pesanan->status, ['selesai', 'dibatalkan'])) {
            $penugasan = $pesanan->penugasanFotografer->first();
            if ($penugasan) {
                $fotograferId = $penugasan->fotografer_id;
                $tanggal = $pesanan->tanggal_acara;

                // Check if this photographer has ANY OTHER active (not finished/cancelled) orders today
                $otherActiveOrders = \App\Models\PenugasanFotografer::where('fotografer_id', $fotograferId)
                    ->whereHas('pesanan', function ($q) use ($tanggal, $pesanan) {
                        $q->where('tanggal_acara', $tanggal)
                            ->whereIn('status', ['dikonfirmasi', 'berlangsung'])
                            ->where('id', '!=', $pesanan->id);
                    })->exists();

                if (! $otherActiveOrders) {
                    \App\Models\KetersediaanFotografer::where('fotografer_id', $fotograferId)
                        ->whereDate('tanggal', $tanggal)
                        ->update(['status' => 'tersedia']);
                }
            }
        }

        return redirect()->back()
            ->with('success', 'Pesanan berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $this->service->delete($id);

        if (request()->ajax() || request()->wantsJson()) {
            return \App\Helpers\ResponseHelper::success(null, 'Pesanan berhasil dihapus!');
        }

        return redirect()->route('pesanan.index')
            ->with('success', 'Pesanan berhasil dihapus!');
    }
}
