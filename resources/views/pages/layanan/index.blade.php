@extends('layouts/layoutMaster')

@section('title', 'Manajemen Layanan')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">
            <span class="text-muted fw-light">Master Data /</span> Daftar Layanan
        </h4>
        <a href="{{ route('layanan.create') }}" class="btn btn-primary">
            <i class="ri-add-line me-1"></i> Tambah Layanan
        </a>
    </div>

    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-0">Daftar Layanan</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th style="width: 50px">#</th>
                        <th>Nama Layanan</th>
                        <th>Kategori</th>
                        <th>Tipe</th>
                        <th>Deskripsi</th>
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
                            <span class="badge bg-label-primary">{{ $item->kategori->nama ?? '-' }}</span>
                        </td>
                        <td>
                            @if($item->tipe == 'fotografi')
                                <span class="badge bg-info"><i class="ri-camera-line me-1"></i>Fotografi</span>
                            @else
                                <span class="badge bg-warning"><i class="ri-video-line me-1"></i>Videografi</span>
                            @endif
                        </td>
                        <td>
                            <small class="text-muted text-truncate d-inline-block" style="max-width: 250px">
                                {{ $item->deskripsi ?? '-' }}
                            </small>
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
                                <a href="{{ route('layanan.edit', $item->id) }}" class="btn btn-sm btn-outline-primary">
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
                                <i class="ri-service-line ri-3x mb-2"></i>
                                <p>Belum ada layanan yang ditambahkan.</p>
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
            let url = "{{ route('layanan.destroy', ':id') }}".replace(':id', id);

            window.AlertHandler.confirm(
                'Hapus Layanan?',
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
