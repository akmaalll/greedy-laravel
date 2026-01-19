<?php

namespace App\Http\Controllers;

use App\Services\KetersediaanFotograferService;
use App\Http\Requests\KetersediaanFotograferRequest;
use Illuminate\Http\Request;

class KetersediaanFotograferController extends Controller
{
    public function __construct(
        protected KetersediaanFotograferService $service,
        protected \App\Services\UserService $userService,
        protected \App\Services\SchedulingService $schedulingService
    ) {}

    /**
     * Generate monthly availability schedule.
     */
    public function generateMonthly(Request $request)
    {
        try {
            $year = $request->input('year', now()->year);
            $month = $request->input('month', now()->month);
            
            // If photographer, only generate for themselves. If admin, balanced for all.
            $fotograferId = auth()->user()->role_id == 4 ? auth()->id() : null;

            $this->schedulingService->generateMonthlyAvailability($year, $month, $fotograferId);

            return redirect()->route('ketersediaan.index')
                ->with('success', 'Jadwal bulanan berhasil digenerate otomatis!');
        } catch (\Exception $e) {
            return redirect()->route('ketersediaan.index')
                ->with('error', 'Gagal generate jadwal: ' . $e->getMessage());
        }
    }

    public function index()
    {
        $data = $this->service->all();
        if (auth()->user()->role_id == 4) {
            $data = $data->where('fotografer_id', auth()->id());
        }
        return view('pages.ketersediaan.index', compact('data'));
    }

    
    public function create()
    {
        $photographers = $this->userService->getPhotographers();
        return view('pages.ketersediaan.create', compact('photographers'));
    }

    public function store(KetersediaanFotograferRequest $request)
    {
        $data = $request->validated();
        
        // If admin, use input photographer. If photographer, force auth user.
        if (auth()->user()->role_id === 2) {
             $request->validate([
                'fotografer_id' => 'required|exists:users,id'
             ]);
             $data['fotografer_id'] = $request->fotografer_id;
        } else {
             $data['fotografer_id'] = auth()->id();
        }

        $this->service->create($data);

        return redirect()->route('ketersediaan.index')
            ->with('success', 'Jadwal ketersediaan berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $data = $this->service->find($id);
        $photographers = $this->userService->getPhotographers();
        return view('pages.ketersediaan.edit', compact('data', 'photographers'));
    }

    public function update(KetersediaanFotograferRequest $request, $id)
    {
        $data = $request->validated();
        if (auth()->user()->role_id === 2) {
             $request->validate([
                'fotografer_id' => 'required|exists:users,id'
             ]);
             $data['fotografer_id'] = $request->fotografer_id;
        }

        $this->service->update($id, $data);
        return redirect()->route('ketersediaan.index')
            ->with('success', 'Jadwal ketersediaan diperbarui!');
    }

    public function destroy($id)
    {
        $this->service->delete($id);
        return redirect()->route('ketersediaan.index')
            ->with('success', 'Jadwal ketersediaan dihapus!');
    }
}
