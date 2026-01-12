@extends('layouts/layoutMaster')

@section('title', 'Laporan Kinerja Fotografer')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">
            <span class="text-muted fw-light">Laporan /</span> Performa Fotografer
        </h4>
        <button class="btn btn-outline-primary" onclick="window.print()">
            <i class="ri-printer-line me-1"></i> Cetak Laporan
        </button>
    </div>

    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-0">Statistik Penugasan & Rating</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Fotografer</th>
                        <th class="text-center">Total Tugas</th>
                        <th class="text-center">Rata-rata Rating</th>
                        <th>Status Performa</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $item)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm me-3">
                                    <span class="avatar-initial rounded-circle bg-label-info">{{ substr($item->name, 0, 1) }}</span>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold">{{ $item->name }}</h6>
                                    <small class="text-muted">{{ $item->email }}</small>
                                </div>
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-label-primary h6 mb-0">{{ $item->penugasan_count }}</span>
                        </td>
                        <td class="text-center">
                            <div class="text-warning h5 mb-0">
                                @php $rating = round($item->rating_diterima_avg_rating, 1) @endphp
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="ri-star-{{ $i <= $rating ? 'fill' : 'line' }}"></i>
                                @endfor
                                <span class="ms-1 text-dark small">({{ $rating ?: '0.0' }})</span>
                            </div>
                        </td>
                        <td>
                            @php
                                $status = 'Cukup';
                                $class = 'bg-label-info';
                                if($rating >= 4.5) { $status = 'Sangat Baik'; $class = 'bg-label-success'; }
                                elseif($rating >= 3.5) { $status = 'Baik'; $class = 'bg-label-primary'; }
                                elseif($rating == 0) { $status = 'Belum Ada Penilaian'; $class = 'bg-label-secondary'; }
                            @endphp
                            <span class="badge {{ $class }}">{{ $status }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5">Belum ada fotografer terdaftar.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
@media print {
    .container-p-y { padding-top: 0 !important; }
    .card { border: none !important; box-shadow: none !important; }
}
</style>
@endsection
