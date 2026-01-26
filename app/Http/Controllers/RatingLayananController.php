<?php

namespace App\Http\Controllers;

use App\Services\RatingLayananService;
use App\Services\PesananService;
use App\Http\Requests\RatingLayananRequest;
use Illuminate\Http\Request;

class RatingLayananController extends Controller
{
    public function __construct(
        protected RatingLayananService $service,
        protected PesananService $pesananService
    ) {}

    public function index()
    {
        $data = $this->service->all();
        return view('pages.rating-layanan.index', compact('data'));
    }

    public function create(Request $request)
    {
        $pesanan = $this->pesananService->find($request->pesanan_id);
        return view('pages.rating-layanan.create', compact('pesanan'));
    }

    public function store(RatingLayananRequest $request)
    {
        $data = $request->validated();
        $data['klien_id'] = auth()->id();

        $this->service->create($data);

        return redirect()->route('pesanan.show', $request->pesanan_id)
            ->with('success', 'Terima kasih atas ulasan Anda untuk layanan kami!');
    }
}
