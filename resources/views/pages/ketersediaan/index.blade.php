@extends('layouts/layoutMaster')

@section('title', 'Ketersediaan Saya')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">
                <span class="text-muted fw-light">Penjadwalan /</span> Ketersediaan Saya
            </h4>
            <div class="d-flex gap-2">
                <form action="{{ route('ketersediaan.generate') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-primary"
                        onclick="return confirm('Generate jadwal otomatis untuk bulan ini?')">
                        <i class="ri-calendar-event-line me-1"></i> Generate Bulanan
                    </button>
                </form>
                <a href="{{ route('ketersediaan.create') }}" class="btn btn-primary">
                    <i class="ri-add-line me-1"></i> Tambah Ketersediaan
                </a>
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
                                <td colspan="6" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="ri-calendar-line ri-3x mb-2"></i>
                                        <p>Anda belum mengisi jadwal ketersediaan.</p>
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
