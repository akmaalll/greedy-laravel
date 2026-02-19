<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaketLayananRequest;
use App\Services\FileUploadService;
use App\Services\LayananService;
use App\Services\PaketLayananService;
use Illuminate\Support\Facades\Storage;

class PaketLayananController extends Controller
{
    public function __construct(
        protected PaketLayananService $service,
        protected LayananService $layananService,
        protected FileUploadService $fileUploadService
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

        // Filter empty fitur items and handle the array
        if ($request->has('fitur') && is_array($request->fitur)) {
            $data['fitur'] = array_values(array_filter($request->fitur));
        }

        if ($request->hasFile('gambar')) {
            $media = $this->fileUploadService->upload($request->file('gambar'), 'paket-layanan', 'public', [
                'width' => 800,
                'height' => 800,
                'crop' => false,
            ]);
            $data['gambar'] = $media->path;
        }

        if ($request->hasFile('video')) {
            $media = $this->fileUploadService->upload($request->file('video'), 'paket-layanan', 'public');
            $data['video'] = $media->path;
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
        $paketLayanan = $this->service->find($id);
        $data = $request->validated();
        $data['is_aktif'] = $request->boolean('is_aktif');

        // Filter empty fitur items and handle the array
        if ($request->has('fitur') && is_array($request->fitur)) {
            $data['fitur'] = array_values(array_filter($request->fitur));
        }

        if ($request->hasFile('gambar')) {
            // Delete old image
            if ($paketLayanan->gambar && Storage::disk('public')->exists($paketLayanan->gambar)) {
                Storage::disk('public')->delete($paketLayanan->gambar);
            }

            $media = $this->fileUploadService->upload($request->file('gambar'), 'paket-layanan', 'public', [
                'width' => 800,
                'height' => 800,
                'crop' => false,
            ]);
            $data['gambar'] = $media->path;
        }

        if ($request->hasFile('video')) {
            // Delete old video
            if ($paketLayanan->video && Storage::disk('public')->exists($paketLayanan->video)) {
                Storage::disk('public')->delete($paketLayanan->video);
            }

            $media = $this->fileUploadService->upload($request->file('video'), 'paket-layanan', 'public');
            $data['video'] = $media->path;
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
        $paketLayanan = $this->service->find($id);

        // Delete associated files
        if ($paketLayanan->gambar && Storage::disk('public')->exists($paketLayanan->gambar)) {
            Storage::disk('public')->delete($paketLayanan->gambar);
        }
        if ($paketLayanan->video && Storage::disk('public')->exists($paketLayanan->video)) {
            Storage::disk('public')->delete($paketLayanan->video);
        }

        $this->service->delete($id);

        if (request()->ajax() || request()->wantsJson()) {
            return \App\Helpers\ResponseHelper::success(null, 'Paket Layanan berhasil dihapus!');
        }

        return redirect()->route('paket-layanan.index')
            ->with('success', 'Paket Layanan berhasil dihapus!');
    }
}
