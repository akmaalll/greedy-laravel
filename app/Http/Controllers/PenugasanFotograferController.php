<?php

namespace App\Http\Controllers;

use App\Services\PenugasanFotograferService;
use App\Services\PesananService;
use App\Http\Requests\PenugasanFotograferRequest;
use Illuminate\Http\Request;

class PenugasanFotograferController extends Controller
{
    public function __construct(
        protected PenugasanFotograferService $service,
        protected PesananService $pesananService
    ) {}

    public function index()
    {
        $data = $this->service->all();
        // If photographer, only show their assignments
        if (auth()->user()->role->slug == 'photographer') {
            $data = $data->where('fotografer_id', auth()->id());
        }
        return view('pages.penugasan-fotografer.index', compact('data'));
    }

    public function show($id)
    {
        $data = $this->service->find($id);
        return view('pages.penugasan-fotografer.show', compact('data'));
    }

    public function update(Request $request, $id)
    {
        // Simple update for status (accept/reject/finish)
        $this->service->update($id, $request->only('status', 'catatan'));

        return redirect()->back()->with('success', 'Status penugasan updated!');
    }
}
