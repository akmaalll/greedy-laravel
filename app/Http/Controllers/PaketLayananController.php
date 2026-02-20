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
            $gambarPaths = [];
            foreach ($request->file('gambar') as $file) {
                $media = $this->fileUploadService->upload($file, 'paket-layanan', 'public', [
                    'width' => 800,
                    'height' => 800,
                    'crop' => false,
                ]);
                $gambarPaths[] = $media->path;
            }
            $data['gambar'] = $gambarPaths;
        }

        if ($request->hasFile('video')) {
            $videoPaths = [];
            foreach ($request->file('video') as $file) {
                $media = $this->fileUploadService->upload($file, 'paket-layanan', 'public');
                $videoPaths[] = $media->path;
            }
            $data['video'] = $videoPaths;
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

        // Handle Gambar (Images)
        $currentGambar = is_array($paketLayanan->gambar) ? $paketLayanan->gambar : [];
        $existingGambar = $request->input('existing_gambar', []);
        
        // Delete images that are no longer in existing_gambar
        foreach ($currentGambar as $oldPath) {
            if (!in_array($oldPath, $existingGambar)) {
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }
        }

        $newGambarPaths = [];
        if ($request->hasFile('gambar')) {
            foreach ($request->file('gambar') as $file) {
                $media = $this->fileUploadService->upload($file, 'paket-layanan', 'public', [
                    'width' => 800,
                    'height' => 800,
                    'crop' => false,
                ]);
                $newGambarPaths[] = $media->path;
            }
        }
        $data['gambar'] = array_merge($existingGambar, $newGambarPaths);

        // Handle Video
        $currentVideo = is_array($paketLayanan->video) ? $paketLayanan->video : [];
        $existingVideo = $request->input('existing_video', []);

        // Delete videos that are no longer in existing_video
        foreach ($currentVideo as $oldPath) {
            if (!in_array($oldPath, $existingVideo)) {
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }
        }

        $newVideoPaths = [];
        if ($request->hasFile('video')) {
            foreach ($request->file('video') as $file) {
                $media = $this->fileUploadService->upload($file, 'paket-layanan', 'public');
                $newVideoPaths[] = $media->path;
            }
        }
        $data['video'] = array_merge($existingVideo, $newVideoPaths);

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
        if ($paketLayanan->gambar && is_array($paketLayanan->gambar)) {
            foreach ($paketLayanan->gambar as $g) {
                if (Storage::disk('public')->exists($g)) {
                    Storage::disk('public')->delete($g);
                }
            }
        }
        if ($paketLayanan->video && is_array($paketLayanan->video)) {
            foreach ($paketLayanan->video as $v) {
                if (Storage::disk('public')->exists($v)) {
                    Storage::disk('public')->delete($v);
                }
            }
        }

        $this->service->delete($id);

        if (request()->ajax() || request()->wantsJson()) {
            return \App\Helpers\ResponseHelper::success(null, 'Paket Layanan berhasil dihapus!');
        }

        return redirect()->route('paket-layanan.index')
            ->with('success', 'Paket Layanan berhasil dihapus!');
    }
}
