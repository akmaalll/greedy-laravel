@extends('layouts/layoutMaster')

@section('title', 'Daftar Pesanan')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss'
])
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js'
])
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    @if(auth()->user()->role->slug == 'client')
        {{-- CLIENT VIEW --}}
        <div class="row mb-6">
            <div class="col-12">
                <div class="card bg-primary text-white" style="background: linear-gradient(72.47deg, #696cff 22.16%, rgba(105, 108, 255, 0.7) 76.47%) !important;">
                    <div class="card-body d-flex align-items-center justify-content-between p-6">
                        <div>
                            <h3 class="text-white mb-2">Halo, {{ auth()->user()->name }}! 👋</h3>
                            <p class="mb-0">Abadikan momen spesial Anda dengan layanan fotografi profesional kami.</p>
                        </div>
                        <div class="d-none d-md-block px-4">
                            <i class="ri-camera-lens-line ri-64px opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Katalog Pesanan (Header) -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">Pilih Layanan Kami</h4>
        </div>

        <!-- Service Catalog -->
        <div class="row mb-6">
            @foreach($paketLayanan as $paket)
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card h-100 shadow-none border">
                    <div class="card-body p-5 text-center">
                        <div class="avatar avatar-xl bg-label-primary mx-auto mb-4">
                            <span class="avatar-initial rounded-3"><i class="ri-camera-fill ri-32px"></i></span>
                        </div>
                        <h5 class="card-title fw-bold mb-2">{{ $paket->nama }}</h5>
                        <p class="card-text text-muted small mb-4 text-truncate-2">{{ Str::limit($paket->deskripsi, 80) }}</p>
                        <div class="h4 text-primary fw-bold mb-4">
                            Rp {{ number_format($paket->harga_dasar, 0, ',', '.') }}
                        </div>
                        <a href="{{ route('pesanan.create', ['selected_paket' => $paket->id]) }}" class="btn btn-primary w-100">
                            Pesan Sekarang
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- History Orders Section -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center border-bottom">
                <h5 class="mb-0">Riwayat Pesanan Anda</h5>
            </div>
            <div class="card-datatable table-responsive">
                <table class="table table-hover border-top" id="table-pesanan-client">
                    <thead>
                        <tr>
                            <th>No. Pesanan</th>
                            <th>Tanggal & Waktu</th>
                            <th>Lokasi</th>
                            <th>Status</th>
                            <th>Total Harga</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $item)
                        <tr>
                            <td>
                                <span class="fw-bold text-primary">#{{ $item->nomor_pesanan }}</span>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-semibold">{{ $item->tanggal_acara ? \Carbon\Carbon::parse($item->tanggal_acara)->format('d M Y') : '-' }}</span>
                                    <small class="text-muted">{{ $item->waktu_mulai_acara ?? '-' }}</small>
                                </div>
                            </td>
                            <td class="text-truncate" style="max-width: 200px;">
                                {{ $item->lokasi_acara }}
                            </td>
                            <td>
                                @php
                                    $statusColors = [
                                        'menunggu' => 'warning',
                                        'dikonfirmasi' => 'primary',
                                        'berlangsung' => 'info',
                                        'selesai' => 'success',
                                        'dibatalkan' => 'danger'
                                    ];
                                    $color = $statusColors[$item->status] ?? 'secondary';
                                @endphp
                                <span class="badge bg-label-{{ $color }} text-uppercase">
                                    {{ $item->status }}
                                </span>
                            </td>
                            <td>
                                <span class="fw-bold">Rp {{ number_format($item->total_harga, 0, ',', '.') }}</span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="{{ route('pesanan.show', $item->id) }}" class="btn btn-sm btn-outline-info" title="Detail">
                                        <i class="ri-eye-line"></i>
                                    </a>
                                    @if($item->status == 'menunggu')
                                    <a href="{{ route('pesanan.edit', $item->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="ri-pencil-line"></i>
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        {{-- Handled by DataTables --}}
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    @else
        {{-- ADMIN & PHOTOGRAPHER VIEW (Original Table) --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">
                <span class="text-muted fw-light">Transaksi /</span> Daftar Pesanan
            </h4>
            @if(auth()->user()->role->slug == 'admin')
            <a href="{{ route('pesanan.create') }}" class="btn btn-primary">
                <i class="ri-add-line me-1"></i> Buat Pesanan
            </a>
            @endif
        </div>

        <div class="card">
            <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Semua Pesanan</h5>
            </div>
            <div class="card-datatable table-responsive">
                <table class="table table-hover border-top" id="table-pesanan-admin">
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
                                    @if(auth()->user()->role->slug == 'admin')
                                    <a href="{{ route('pesanan.edit', $item->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="ri-pencil-line"></i>
                                    </a>
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
                        {{-- Handled by DataTables --}}
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection

@section('page-script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize DataTables
        const tableClient = $('#table-pesanan-client');
        if (tableClient.length) {
            tableClient.DataTable({
                responsive: true,
                order: [[1, 'desc']], // Order by date
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Cari pesanan...",
                    paginate: {
                        previous: '<i class="ri-arrow-left-s-line"></i>',
                        next: '<i class="ri-arrow-right-s-line"></i>'
                    }
                }
            });
        }

        const tableAdmin = $('#table-pesanan-admin');
        if (tableAdmin.length) {
            tableAdmin.DataTable({
                responsive: true,
                order: [[2, 'desc']], // Order by date column
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Cari...",
                    paginate: {
                        previous: '<i class="ri-arrow-left-s-line"></i>',
                        next: '<i class="ri-arrow-right-s-line"></i>'
                    }
                }
            });
        }

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

