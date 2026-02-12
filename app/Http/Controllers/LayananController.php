<?php

namespace App\Http\Controllers;

use App\Services\LayananService;
use App\Services\KategoriService;
use App\Http\Requests\LayananRequest;
use Illuminate\Http\Request;

class LayananController extends Controller
{
    public function __construct(
        protected LayananService $service,
        protected KategoriService $kategoriService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = $this->service->all();
        return view('pages.layanan.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $kategori = $this->kategoriService->all();
        return view('pages.layanan.create', compact('kategori'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(LayananRequest $request)
    {
        $data = $request->validated();
        $data['is_aktif'] = $request->boolean('is_aktif');

        $this->service->create($data);

        return redirect()->route('layanan.index')
            ->with('success', 'Layanan berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $data = $this->service->find($id);
        return view('pages.layanan.show', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $data = $this->service->find($id);
        $kategori = $this->kategoriService->all();
        return view('pages.layanan.edit', compact('data', 'kategori'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(LayananRequest $request, $id)
    {
        $data = $request->validated();
        $data['is_aktif'] = $request->boolean('is_aktif');

        $this->service->update($id, $data);

        return redirect()->route('layanan.index')
            ->with('success', 'Layanan berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $this->service->delete($id);

        if (request()->ajax() || request()->wantsJson()) {
            return \App\Helpers\ResponseHelper::success(null, 'Layanan berhasil dihapus!');
        }

        return redirect()->route('layanan.index')
            ->with('success', 'Layanan berhasil dihapus!');
    }
}
