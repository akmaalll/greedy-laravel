<?php

namespace App\Http\Controllers;

use App\Services\JadwalPesananService;
use App\Http\Requests\JadwalPesananRequest;
use Illuminate\Http\Request;

class JadwalPesananController extends Controller
{
    public function __construct(
        protected JadwalPesananService $service
    ) {}

    public function index()
    {
        $data = $this->service->all();
        return view('pages.jadwal-pesanan.index', compact('data'));
    }

    public function create(Request $request)
    {
        $pesananId = $request->pesanan_id;
        return view('pages.jadwal-pesanan.create', compact('pesananId'));
    }

    public function store(JadwalPesananRequest $request)
    {
        $this->service->create($request->validated());
        return redirect()->route('jadwal-pesanan.index')->with('success', 'Jadwal pesanan berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $data = $this->service->find($id);
        return view('pages.jadwal-pesanan.edit', compact('data'));
    }

    public function update(JadwalPesananRequest $request, $id)
    {
        $this->service->update($id, $request->validated());
        return redirect()->route('jadwal-pesanan.index')->with('success', 'Jadwal pesanan berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $this->service->delete($id);
        return redirect()->route('jadwal-pesanan.index')->with('success', 'Jadwal pesanan berhasil dihapus!');
    }
}
