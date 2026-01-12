<?php

namespace App\Http\Controllers;

use App\Services\PesananService;
use App\Services\PaketLayananService;
use App\Http\Requests\PesananRequest;
use Illuminate\Http\Request;
use App\Models\PaketLayanan;

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
        if (auth()->user()->role->slug == 'client') {
            $data = $data->where('klien_id', auth()->id());
            $paketLayanan = $this->paketService->all()->where('is_aktif', true);
        }

        return view('pages.pesanan.index', compact('data', 'paketLayanan'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $paketLayanan = $this->paketService->all()->where('is_aktif', true);
        $selectedPaketId = $request->query('selected_paket');
        
        return view('pages.pesanan.create', compact('paketLayanan', 'selectedPaketId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PesananRequest $request)
    {
        $data = $request->validated();
        
        // Check availability strictly before booking
        $isAvailable = $this->schedulingService->checkAvailability(
            $data['tanggal_acara'], 
            $data['waktu_mulai_acara'], 
            $data['waktu_selesai_acara'] ?? null
        );

        if (!$isAvailable) {
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
                'subtotal' => $paket->harga_dasar * $item['jumlah']
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

        // Greedy Assignment Trigger: 
        // 1. If status changed to 'dikonfirmasi'
        // 2. OR if status is already 'dikonfirmasi' but has no photographer (Retry logic)
        if ($pesanan->status == 'dikonfirmasi') {
            if ($pesanan->penugasanFotografer->isEmpty()) {
                $assignment = $this->schedulingService->assignPhotographer($pesanan);
                
                if (!$assignment && $oldStatus != 'dikonfirmasi') {
                    return redirect()->back()
                        ->with('warning', 'Pesanan dikonfirmasi, namun tidak ada fotografer tersedia secara otomatis. Silakan tugaskan secara manual.');
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
