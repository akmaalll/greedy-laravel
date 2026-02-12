@extends('layouts/layoutMaster')

@section('title', 'Daftar Pembayaran')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">
            <span class="text-muted fw-light">Transaksi /</span> Pembayaran
        </h4>
    </div>

    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-0">Riwayat Pembayaran</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID Pesanan</th>
                        <th>Klien</th>
                        <th>Tgl Bayar</th>
                        <th>Jumlah</th>
                        <th>Metode</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $item)
                    <tr>
                        <td>
                            <span class="fw-bold text-primary">{{ $item->pesanan->nomor_pesanan }}</span>
                        </td>
                        <td>{{ $item->pesanan->klien->name ?? '-' }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->waktu_bayar)->format('d/m/Y') }}</td>
                        <td class="fw-bold">Rp {{ number_format($item->jumlah, 0, ',', '.') }}</td>
                        <td>{{ strtoupper($item->metode_pembayaran) }}</td>
                        <td>
                            @php
                                $statusClasses = [
                                    'menunggu' => 'bg-label-warning',
                                    'lunas' => 'bg-label-success',
                                    'gagal' => 'bg-label-danger'
                                ];
                                $class = $statusClasses[$item->status] ?? 'bg-label-secondary';
                            @endphp
                            <span class="badge {{ $class }} font-weight-bold">
                                {{ strtoupper($item->status) }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ route('pembayaran.show', $item->id) }}" class="btn btn-sm btn-outline-info">
                                    <i class="ri-eye-line"></i>
                                </a>
                                @if(auth()->user()->role->slug == 'admin')
                                <a href="{{ route('pembayaran.edit', $item->id) }}" class="btn btn-sm btn-outline-warning">
                                    <i class="ri-edit-line"></i>
                                </a>
                                <form action="{{ route('pembayaran.destroy', $item->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus data pembayaran ini?')">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">Belum ada data pembayaran.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
