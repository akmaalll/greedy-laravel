<?php

namespace App\Http\Controllers;

use App\Services\KategoriService;
use App\Http\Requests\KategoriRequest;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    public function __construct(
        protected KategoriService $service
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = $this->service->all();
        return view('pages.kategori.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.kategori.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(KategoriRequest $request)
    {
        $data = $request->validated();
        $data['is_aktif'] = $request->boolean('is_aktif');

        $this->service->create($data);

        return redirect()->route('kategori.index')
            ->with('success', 'Kategori berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $data = $this->service->find($id);
        return view('pages.kategori.show', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $data = $this->service->find($id);
        return view('pages.kategori.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(KategoriRequest $request, $id)
    {
        $data = $request->validated();
        $data['is_aktif'] = $request->boolean('is_aktif');

        $this->service->update($id, $data);

        return redirect()->route('kategori.index')
            ->with('success', 'Kategori berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $this->service->delete($id);

        if (request()->ajax() || request()->wantsJson()) {
            return \App\Helpers\ResponseHelper::success(null, 'Kategori berhasil dihapus!');
        }

        return redirect()->route('kategori.index')
            ->with('success', 'Kategori berhasil dihapus!');
    }
}
