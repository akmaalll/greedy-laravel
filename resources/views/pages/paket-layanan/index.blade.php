@extends('layouts/layoutMaster')

@section('title', 'Manajemen Paket Layanan')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">
            <span class="text-muted fw-light">Master Data /</span> Daftar Paket Layanan
        </h4>
        <a href="{{ route('paket-layanan.create') }}" class="btn btn-primary">
            <i class="ri-add-line me-1"></i> Tambah Paket
        </a>
    </div>

    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-0">Daftar Paket Layanan</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th style="width: 50px">#</th>
                        <th>Nama Paket</th>
                        <th>Layanan</th>
                        <th>Durasi</th>
                        <th>Harga</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <span class="fw-bold">{{ $item->nama }}</span>
                        </td>
                        <td>
                            <span class="badge bg-label-secondary">{{ $item->layanan->nama ?? '-' }}</span>
                        </td>
                        <td>
                            {{ $item->nilai_durasi }} {{ ucfirst($item->tipe_durasi) }}
                        </td>
                        <td>
                            <div class="mb-1">Dasar: <strong>Rp {{ number_format($item->harga_dasar, 0, ',', '.') }}</strong></div>
                            @if($item->harga_overtime)
                                <small class="text-muted">Overtime: Rp {{ number_format($item->harga_overtime, 0, ',', '.') }} / {{ $item->satuan_overtime }}</small>
                            @endif
                        </td>
                        <td>
                            @if($item->is_aktif)
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-secondary">Non-Aktif</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ route('paket-layanan.edit', $item->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="ri-pencil-line"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger delete-record" 
                                    data-id="{{ $item->id }}" 
                                    data-name="{{ $item->nama }}">
                                    <i class="ri-delete-bin-line"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="text-muted">
                                <i class="ri-price-tag-3-line ri-3x mb-2"></i>
                                <p>Belum ada paket layanan yang ditambahkan.</p>
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
            let url = "{{ route('paket-layanan.destroy', ':id') }}".replace(':id', id);

            window.AlertHandler.confirm(
                'Hapus Paket Layanan?',
                `Apakah Anda yakin ingin menghapus "${name}"? Data yang dihapus tidak dapat dikembalikan.`,
                'Ya, Hapus!',
                function() {
                    $.ajax({
                        url: url,
                        method: 'DELETE',
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
