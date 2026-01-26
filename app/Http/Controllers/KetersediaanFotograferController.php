<?php

namespace App\Http\Controllers;

use App\Http\Requests\KetersediaanFotograferRequest;
use App\Models\KetersediaanFotografer;
use App\Services\KetersediaanFotograferService;
use Illuminate\Http\Request;

class KetersediaanFotograferController extends Controller
{
    public function __construct(
        protected KetersediaanFotograferService $service,
        protected \App\Services\UserService $userService,
        protected \App\Services\SchedulingService $schedulingService
    ) {}

    /**
     * Get existing dates for date picker validation
     */
    public function getExistingDates()
    {
        $query = KetersediaanFotografer::query();

        // If photographer, only get their dates
        if (auth()->user()->role_id == 4) {
            $query->where('fotografer_id', auth()->id());
        }

        $dates = $query->pluck('tanggal')
            ->map(fn ($date) => \Carbon\Carbon::parse($date)->format('Y-m-d'))
            ->unique()
            ->values()
            ->toArray();

        return response()->json($dates);
    }

    /**
     * Generate monthly availability schedule with date range.
     */
    public function generateMonthly(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        try {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');

            // If photographer, only generate for themselves. If admin, for all photographers.
            $fotograferId = auth()->user()->role_id == 4 ? auth()->id() : null;

            $this->schedulingService->generateRangeAvailability($startDate, $endDate, $fotograferId);

            return redirect()->route('ketersediaan.index')
                ->with('success', 'Jadwal berhasil digenerate untuk tanggal '.
                    \Carbon\Carbon::parse($startDate)->format('d M Y').' - '.
                    \Carbon\Carbon::parse($endDate)->format('d M Y'));
        } catch (\Exception $e) {
            return redirect()->route('ketersediaan.index')
                ->with('error', 'Gagal generate jadwal: '.$e->getMessage());
        }
    }

    public function index(Request $request)
    {
        // Start with query builder instead of collection
        $query = KetersediaanFotografer::with('fotografer');

        // Filter by photographer role
        if (auth()->user()->role_id == 4) {
            $query->where('fotografer_id', auth()->id());
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('tanggal', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('tanggal', '<=', $request->end_date);
        }

        // Order by date descending and execute query
        $data = $query->orderBy('tanggal', 'desc')->get();

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
                'fotografer_id' => 'required|exists:users,id',
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
                'fotografer_id' => 'required|exists:users,id',
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
