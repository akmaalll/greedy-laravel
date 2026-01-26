<?php

namespace App\Http\Controllers;

use App\Services\PembayaranService;
use App\Http\Requests\PembayaranRequest;
use Illuminate\Http\Request;

class PembayaranController extends Controller
{
    public function __construct(
        protected PembayaranService $service
    ) {}

    public function index()
    {
        $query = \App\Models\Pembayaran::with('pesanan.klien');
        
        if (auth()->user()->role_id == 3) {
            $query->whereHas('pesanan', function($q) {
                $q->where('klien_id', auth()->id());
            });
        }
        
        $data = $query->latest()->get();
        return view('pages.pembayaran.index', compact('data'));
    }

    public function create(Request $request)
    {
        $pesananId = $request->pesanan_id;
        $pesanan = \App\Models\Pesanan::find($pesananId);
        return view('pages.pembayaran.create', compact('pesananId', 'pesanan'));
    }

    public function store(PembayaranRequest $request)
    {
        $data = $request->validated();
        
        // Handle defaults for client
        if (auth()->user()->role_id == 3) {
            $data['status'] = 'menunggu';
            if (empty($data['waktu_bayar'])) {
                $data['waktu_bayar'] = now();
            }
        }
        
        $this->service->create($data);
        return redirect()->route('pembayaran.index')->with('success', 'Pembayaran berhasil dikirim! Menunggu konfirmasi admin.');
    }

    public function show($id)
    {
        $data = $this->service->find($id);
        return view('pages.pembayaran.show', compact('data'));
    }

    public function edit($id)
    {
        $data = $this->service->find($id);
        $pesanan = $data->pesanan;
        return view('pages.pembayaran.edit', compact('data', 'pesanan'));
    }

    public function update(PembayaranRequest $request, $id)
    {
        $data = $request->validated();
        $this->service->update($id, $data);
        return redirect()->route('pembayaran.index')->with('success', 'Data pembayaran berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $this->service->delete($id);
        return redirect()->route('pembayaran.index')->with('success', 'Data pembayaran berhasil dihapus!');
    }
}
