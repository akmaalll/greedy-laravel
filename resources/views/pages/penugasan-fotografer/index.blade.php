@extends('layouts/layoutMaster')

@section('title', 'Penugasan Fotografer')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">
            <span class="text-muted fw-light">Jadwal /</span> Penugasan Fotografer
        </h4>
    </div>

    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-0">Daftar Penugasan</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th style="width: 150px">No. Pesanan</th>
                        <th>Fotografer</th>
                        <th>Tanggal Acara</th>
                        <th>Status</th>
                        <th>Catatan Sistem</th>
                        <th>Ditugaskan</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $item)
                    <tr>
                        <td>
                            <span class="fw-bold text-primary">{{ $item->pesanan->nomor_pesanan }}</span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm me-2">
                                    <span class="avatar-initial rounded-circle bg-label-info">{{ substr($item->fotografer->name, 0, 1) }}</span>
                                </div>
                                <span class="fw-semibold">{{ $item->fotografer->name }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <span>{{ $item->pesanan->tanggal_acara ? \Carbon\Carbon::parse($item->pesanan->tanggal_acara)->format('d M Y') : '-' }}</span>
                                <small class="text-muted">{{ $item->pesanan->waktu_mulai_acara ?? '-' }}</small>
                            </div>
                        </td>
                        <td>
                            @php
                                $statusClasses = [
                                    'ditugaskan' => 'bg-label-warning',
                                    'diterima' => 'bg-label-info',
                                    'ditolak' => 'bg-label-danger',
                                    'selesai' => 'bg-label-success'
                                ];
                                $class = $statusClasses[$item->status] ?? 'bg-label-secondary';
                            @endphp
                            <span class="badge {{ $class }} font-weight-bold">
                                {{ strtoupper($item->status) }}
                            </span>
                        </td>
                        <td>
                            <small class="text-muted">{{ $item->catatan ?: '-' }}</small>
                        </td>
                        <td>
                            <small class="text-muted">{{ $item->created_at->format('d/m/y H:i') }}</small>
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ route('penugasan-fotografer.show', $item->id) }}" class="btn btn-sm btn-outline-info">
                                    <i class="ri-eye-line"></i>
                                </a>
                                <!-- Auto-accepted by system, no buttons needed for 'ditugaskan' -->
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="text-muted">
                                <i class="ri-calendar-todo-line ri-3x mb-2"></i>
                                <p>Belum ada penugasan fotografer.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
