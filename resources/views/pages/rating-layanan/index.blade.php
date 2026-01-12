@extends('layouts/layoutMaster')

@section('title', 'Daftar Rating Layanan')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">
            <span class="text-muted fw-light">Rating /</span> Rating Layanan
        </h4>
    </div>

    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-0">Kepuasan Layanan Klien</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Pesanan</th>
                        <th>Klien</th>
                        <th class="text-center">Rating</th>
                        <th>Ulasan / Saran</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $item)
                    <tr>
                        <td>
                            <span class="fw-bold text-info">{{ $item->pesanan->nomor_pesanan }}</span>
                        </td>
                        <td>{{ $item->klien->name }}</td>
                        <td class="text-center">
                            <div class="text-warning">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="ri-star-{{ $i <= $item->rating ? 'fill' : 'line' }}"></i>
                                @endfor
                            </div>
                        </td>
                        <td>
                            <small class="text-muted">{{ Str::limit($item->ulasan, 50) ?: '-' }}</small>
                        </td>
                        <td>{{ $item->created_at->format('d/m/Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">Belum ada ulasan untuk layanan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
