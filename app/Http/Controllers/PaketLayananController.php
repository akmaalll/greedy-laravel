<?php

namespace App\Http\Controllers;

use App\Services\PaketLayananService;
use App\Services\LayananService;
use App\Http\Requests\PaketLayananRequest;
use Illuminate\Http\Request;

class PaketLayananController extends Controller
{
    public function __construct(
        protected PaketLayananService $service,
        protected LayananService $layananService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = $this->service->all();
        return view('pages.paket-layanan.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $layanan = $this->layananService->all();
        return view('pages.paket-layanan.create', compact('layanan'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PaketLayananRequest $request)
    {
        $data = $request->validated();
        $data['is_aktif'] = $request->boolean('is_aktif');
        
        // Convert fitur array to JSON if provided
        if ($request->has('fitur') && is_array($request->fitur)) {
            $data['fitur'] = json_encode(array_filter($request->fitur));
        }

        $this->service->create($data);

        return redirect()->route('paket-layanan.index')
            ->with('success', 'Paket Layanan berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $data = $this->service->find($id);
        return view('pages.paket-layanan.show', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $data = $this->service->find($id);
        $layanan = $this->layananService->all();
        return view('pages.paket-layanan.edit', compact('data', 'layanan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PaketLayananRequest $request, $id)
    {
        $data = $request->validated();
        $data['is_aktif'] = $request->boolean('is_aktif');
        
        // Convert fitur array to JSON if provided
        if ($request->has('fitur') && is_array($request->fitur)) {
            $data['fitur'] = json_encode(array_filter($request->fitur));
        }

        $this->service->update($id, $data);

        return redirect()->route('paket-layanan.index')
            ->with('success', 'Paket Layanan berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $this->service->delete($id);

        if (request()->wantsJson()) {
            return \App\Helpers\ResponseHelper::success(null, 'Paket Layanan berhasil dihapus!');
        }

        return redirect()->route('paket-layanan.index')
            ->with('success', 'Paket Layanan berhasil dihapus!');
    }
}
