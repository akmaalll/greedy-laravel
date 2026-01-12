@extends('layouts/layoutMaster')

@section('title', 'Daftar Pesanan')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">
            <span class="text-muted fw-light">Transaksi /</span> Daftar Pesanan
        </h4>
        @if(auth()->user()->role->slug == 'client' || auth()->user()->role->slug == 'admin')
        <a href="{{ route('pesanan.create') }}" class="btn btn-primary">
            <i class="ri-add-line me-1"></i> Buat Pesanan
        </a>
        @endif
    </div>

    <div class="card">
        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Semua Pesanan</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>No. Pesanan</th>
                        <th>Klien</th>
                        <th>Tanggal Acara</th>
                        <th>Status</th>
                        <th>Total Harga</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $item)
                    <tr>
                        <td>
                            <a href="{{ route('pesanan.show', $item->id) }}" class="fw-bold text-primary">
                                {{ $item->nomor_pesanan }}
                            </a>
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <span class="fw-semibold">{{ $item->klien->name ?? 'N/A' }}</span>
                                <small class="text-muted">{{ $item->lokasi_acara }}</small>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <span>{{ $item->tanggal_acara ? \Carbon\Carbon::parse($item->tanggal_acara)->format('d M Y') : '-' }}</span>
                                <small class="text-muted">{{ $item->waktu_mulai_acara ?? '-' }}</small>
                            </div>
                        </td>
                        <td>
                            @php
                                $statusClasses = [
                                    'menunggu' => 'bg-label-warning',
                                    'dikonfirmasi' => 'bg-label-primary',
                                    'berlangsung' => 'bg-label-info',
                                    'selesai' => 'bg-label-success',
                                    'dibatalkan' => 'bg-label-danger'
                                ];
                                $class = $statusClasses[$item->status] ?? 'bg-label-secondary';
                            @endphp
                            <span class="badge {{ $class }} font-weight-bold">
                                {{ strtoupper($item->status) }}
                            </span>
                        </td>
                        <td>
                            <strong>Rp {{ number_format($item->total_harga, 0, ',', '.') }}</strong>
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ route('pesanan.show', $item->id) }}" class="btn btn-sm btn-outline-info" title="Detail">
                                    <i class="ri-eye-line"></i>
                                </a>
                                @if(auth()->user()->role->slug == 'admin' || (auth()->user()->role->slug == 'client' && $item->status == 'menunggu'))
                                <a href="{{ route('pesanan.edit', $item->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="ri-pencil-line"></i>
                                </a>
                                @endif
                                @if(auth()->user()->role->slug == 'admin')
                                <button type="button" class="btn btn-sm btn-outline-danger delete-record" 
                                    data-id="{{ $item->id }}" 
                                    data-name="{{ $item->nomor_pesanan }}">
                                    <i class="ri-delete-bin-line"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="text-muted">
                                <i class="ri-shopping-cart-line ri-3x mb-2"></i>
                                <p>Belum ada pesanan yang ditemukan.</p>
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

@section('page-script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        $('.delete-record').on('click', function() {
            let id = $(this).data('id');
            let name = $(this).data('name');
            let url = "{{ route('pesanan.destroy', ':id') }}".replace(':id', id);

            window.AlertHandler.confirm(
                'Hapus Pesanan?',
                `Apakah Anda yakin ingin menghapus pesanan "${name}"?`,
                'Ya, Hapus!',
                function() {
                    $.ajax({
                        url: url,
                        method: 'DELETE',
                        dataType: 'json',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            window.AlertHandler.handle(response);
                            setTimeout(() => {
                                window.location.reload();
                            }, 1500);
                        },
                        error: function(xhr) {
                            window.AlertHandler.handle(xhr.responseJSON);
                        }
                    });
                }
            );
        });
    });
</script>
@endsection
