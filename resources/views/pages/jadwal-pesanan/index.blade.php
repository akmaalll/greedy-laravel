@extends('layouts/layoutMaster')

@section('title', 'Jadwal Pesanan')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">
            <span class="text-muted fw-light">Operasional /</span> Jadwal Kegiatan Acara
        </h4>
    </div>

    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-0">Agenda Detail Acara</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>No. Pesanan</th>
                        <th>Kegiatan</th>
                        <th>Tanggal</th>
                        <th>Waktu</th>
                        <th>Lokasi</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $item)
                    <tr>
                        <td>
                            <span class="fw-bold text-info">{{ $item->pesanan->nomor_pesanan }}</span>
                        </td>
                        <td><span class="fw-semibold">{{ $item->kegiatan }}</span></td>
                        <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}</td>
                        <td>
                            <span class="badge bg-label-primary">{{ $item->waktu_mulai }} - {{ $item->waktu_selesai }}</span>
                        </td>
                        <td><small>{{ $item->pesanan->lokasi_acara }}</small></td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ route('jadwal-pesanan.edit', $item->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="ri-pencil-line"></i>
                                </a>
                                <form action="{{ route('jadwal-pesanan.destroy', $item->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus detail jadwal ini?')">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">Belum ada agenda detail khusus.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
