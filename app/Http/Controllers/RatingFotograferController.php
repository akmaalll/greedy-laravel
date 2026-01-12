<?php

namespace App\Http\Controllers;

use App\Services\RatingFotograferService;
use App\Services\PesananService;
use App\Http\Requests\RatingFotograferRequest;
use Illuminate\Http\Request;

class RatingFotograferController extends Controller
{
    public function __construct(
        protected RatingFotograferService $service,
        protected PesananService $pesananService
    ) {}

    public function index()
    {
        $data = $this->service->all();
        return view('pages.rating-fotografer.index', compact('data'));
    }

    public function create(Request $request)
    {
        $pesanan = $this->pesananService->find($request->pesanan_id);
        return view('pages.rating-fotografer.create', compact('pesanan'));
    }

    public function store(RatingFotograferRequest $request)
    {
        $data = $request->validated();
        $data['klien_id'] = auth()->id();

        $this->service->create($data);

        return redirect()->route('pesanan.show', $request->pesanan_id)
            ->with('success', 'Terima kasih atas ulasan Anda untuk fotografer!');
    }
}
