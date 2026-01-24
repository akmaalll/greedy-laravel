@extends('layouts/layoutMaster')

@section('title', 'Ketersediaan Saya')

@section('vendor-style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endsection

@section('vendor-script')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">
                <span class="text-muted fw-light">Penjadwalan /</span> Ketersediaan Saya
            </h4>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#generateModal">
                    <i class="ri-calendar-event-line me-1"></i> Generate Bulanan
                </button>
                <a href="{{ route('ketersediaan.create') }}" class="btn btn-primary">
                    <i class="ri-add-line me-1"></i> Tambah Ketersediaan
                </a>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('ketersediaan.index') }}" id="filterForm">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Tanggal Dari</label>
                            <input type="text" class="form-control" id="filterStartDate" name="start_date"
                                placeholder="Pilih tanggal mulai" value="{{ request('start_date') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tanggal Sampai</label>
                            <input type="text" class="form-control" id="filterEndDate" name="end_date"
                                placeholder="Pilih tanggal akhir" value="{{ request('end_date') }}">
                        </div>
                        <div class="col-md-4 d-flex align-items-end gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="ri-filter-line me-1"></i> Filter
                            </button>
                            <a href="{{ route('ketersediaan.index') }}" class="btn btn-outline-secondary">
                                <i class="ri-refresh-line me-1"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-0">Jadwal Ketersediaan</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            @if (auth()->user()->role->slug == 'admin')
                                <th>Fotografer</th>
                            @endif
                            <th style="width: 150px">Tanggal</th>
                            <th>Waktu Mulai</th>
                            <th>Waktu Selesai</th>
                            <th>Status</th>
                            <th>Catatan</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $item)
                            <tr>
                                @if (auth()->user()->role->slug == 'admin')
                                    <td>{{ $item->fotografer->name }}</td>
                                @endif
                                <td>
                                    <span
                                        class="fw-bold">{{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}</span>
                                </td>
                                <td>{{ $item->waktu_mulai }}</td>
                                <td>{{ $item->waktu_selesai }}</td>
                                <td>
                                    @php
                                        $statusClasses = [
                                            'tersedia' => 'bg-label-success',
                                            'tidak_tersedia' => 'bg-label-danger',
                                            'dipesan' => 'bg-label-primary',
                                        ];
                                        $class = $statusClasses[$item->status] ?? 'bg-label-secondary';
                                    @endphp
                                    <span class="badge {{ $class }} font-weight-bold">
                                        {{ strtoupper($item->status) }}
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted text-truncate d-inline-block" style="max-width: 200px">
                                        {{ $item->catatan ?: '-' }}
                                    </small>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ route('ketersediaan.edit', $item->id) }}"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="ri-pencil-line"></i>
                                        </a>
                                        <form action="{{ route('ketersediaan.destroy', $item->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('Hapus jadwal ini?')">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="ri-calendar-line ri-3x mb-2"></i>
                                        <p>Tidak ada jadwal ketersediaan.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Generate Modal -->
    <div class="modal fade" id="generateModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Generate Jadwal Bulanan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('ketersediaan.generate') }}" method="POST" id="generateForm">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="ri-information-line me-1"></i>
                            Pilih rentang tanggal untuk generate jadwal. Tanggal yang sudah memiliki data tidak akan
                            dibuat ulang.
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tanggal Dari <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="startDate" name="start_date"
                                placeholder="Pilih tanggal mulai" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tanggal Sampai <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="endDate" name="end_date"
                                placeholder="Pilih tanggal akhir" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-calendar-event-line me-1"></i> Generate
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Fetch existing dates from server
            fetch('{{ route('ketersediaan.existing-dates') }}')
                .then(response => response.json())
                .then(existingDates => {
                    // Initialize flatpickr for generate modal
                    const startDatePicker = flatpickr("#startDate", {
                        dateFormat: "Y-m-d",
                        minDate: "today",
                        disable: existingDates,
                        onChange: function(selectedDates, dateStr, instance) {
                            endDatePicker.set('minDate', dateStr);
                        }
                    });

                    const endDatePicker = flatpickr("#endDate", {
                        dateFormat: "Y-m-d",
                        minDate: "today",
                        disable: existingDates
                    });

                    // Initialize flatpickr for filter
                    flatpickr("#filterStartDate", {
                        dateFormat: "Y-m-d",
                        onChange: function(selectedDates, dateStr, instance) {
                            flatpickr("#filterEndDate", {
                                dateFormat: "Y-m-d",
                                minDate: dateStr
                            });
                        }
                    });

                    flatpickr("#filterEndDate", {
                        dateFormat: "Y-m-d"
                    });
                })
                .catch(error => {
                    console.error('Error fetching existing dates:', error);
                    // Initialize without disable if fetch fails
                    flatpickr("#startDate", {
                        dateFormat: "Y-m-d",
                        minDate: "today"
                    });
                    flatpickr("#endDate", {
                        dateFormat: "Y-m-d",
                        minDate: "today"
                    });
                });
        });
    </script>
@endsection
