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
                            <th style="width: 80px">Gambar</th>
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
                                <td>
                                    @if ($item->gambar && is_array($item->gambar) && count($item->gambar) > 0)
                                        <div class="position-relative" style="width: 60px; height: 60px;">
                                            <img src="{{ asset('storage/' . $item->gambar[0]) }}" alt="{{ $item->nama }}"
                                                class="rounded cursor-pointer popup-image"
                                                style="width: 60px; height: 60px; object-fit: cover; border: 2px solid #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                            @if (count($item->gambar) > 1)
                                                <span class="position-absolute bottom-0 end-0 badge rounded-pill bg-primary"
                                                    style="font-size: 10px; padding: 2px 5px;">
                                                    +{{ count($item->gambar) - 1 }}
                                                </span>
                                            @endif
                                        </div>
                                    @elseif($item->gambar && is_string($item->gambar))
                                        <img src="{{ asset('storage/' . $item->gambar) }}" alt="{{ $item->nama }}"
                                            class="rounded cursor-pointer popup-image"
                                            style="width: 60px; height: 60px; object-fit: cover;">
                                    @else
                                        <div class="rounded bg-label-secondary d-flex align-items-center justify-content-center"
                                            style="width: 60px; height: 60px;">
                                            <i class="ri-image-line"></i>
                                        </div>
                                    @endif
                                </td>
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
                                    <div class="mb-1">Dasar: <strong>Rp
                                            {{ number_format($item->harga_dasar, 0, ',', '.') }}</strong></div>
                                    @if ($item->harga_overtime)
                                        <small class="text-muted">Overtime: Rp
                                            {{ number_format($item->harga_overtime, 0, ',', '.') }} /
                                            {{ $item->satuan_overtime }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if ($item->is_aktif)
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary">Non-Aktif</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <button type="button" class="btn btn-sm btn-outline-info show-detail"
                                            data-name="{{ $item->nama }}" data-desc="{{ $item->deskripsi }}"
                                            data-fitur="{{ json_encode($item->fitur) }}"
                                            data-gambar="{{ json_encode(is_array($item->gambar) ? array_map(fn($g) => asset('storage/' . $g), $item->gambar) : ($item->gambar ? [asset('storage/' . $item->gambar)] : [])) }}"
                                            data-video="{{ json_encode(is_array($item->video) ? array_map(fn($v) => asset('storage/' . $v), $item->video) : ($item->video ? [asset('storage/' . $item->video)] : [])) }}">
                                            <i class="ri-eye-line"></i>
                                        </button>
                                        <a href="{{ route('paket-layanan.edit', $item->id) }}"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="ri-pencil-line"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger delete-record"
                                            data-id="{{ $item->id }}" data-name="{{ $item->nama }}">
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

    <!-- Detail Modal -->
    <div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-title">Detail Paket</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="modal-media" class="mb-3 text-center d-none">
                        <div id="modal-images-container" class="d-flex flex-wrap justify-content-center gap-2 mb-2"></div>
                        <div id="modal-videos-container" class="d-flex flex-column gap-2"></div>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold text-muted d-block mb-1">DESKRIPSI</label>
                        <div id="modal-desc" class="p-3 bg-light rounded"></div>
                    </div>
                    <div>
                        <label class="small fw-bold text-muted d-block mb-1">FITUR & LAYANAN</label>
                        <ul id="modal-fitur" class="list-group list-group-flush border rounded mt-2"></ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Image Popup Modal -->
    <div class="modal fade" id="imagePopupModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content bg-transparent border-0 shadow-none">
                <div class="modal-body p-0 text-center position-relative">
                    <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-2"
                        style="z-index: 1060" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div id="imageCarousel" class="carousel slide" data-bs-interval="false">
                        <div class="carousel-inner" id="carousel-images-inner"></div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#imageCarousel"
                            data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#imageCarousel"
                            data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Video Popup Modal -->
    <div class="modal fade" id="videoPopupModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content bg-transparent border-0 shadow-none">
                <div class="modal-body p-0 text-center position-relative">
                    <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-2"
                        style="z-index: 1060" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div id="videoCarousel" class="carousel slide" data-bs-interval="false">
                        <div class="carousel-inner" id="carousel-videos-inner"></div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#videoCarousel"
                            data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#videoCarousel"
                            data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-style')
    <style>
        .cursor-pointer {
            cursor: pointer;
        }

        .btn-close-white {
            filter: invert(1) grayscale(100%) brightness(200%);
        }

        .popup-image:hover {
            opacity: 0.8;
            transition: 0.3s;
        }

        /* Carousel Controls Visibility */
        .carousel-control-prev,
        .carousel-control-next {
            width: 50px;
            height: 50px;
            background: rgba(0, 0, 0, 0.5);
            border-radius: 50%;
            top: 50%;
            transform: translateY(-50%);
            margin: 0 15px;
            opacity: 0.8;
            z-index: 1070;
        }

        .carousel-control-prev:hover,
        .carousel-control-next:hover {
            background: rgba(0, 0, 0, 0.8);
            opacity: 1;
        }

        .carousel-item img,
        .carousel-item video {
            max-height: 85vh;
            object-fit: contain;
        }
    </style>
@endsection

@section('page-script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
            const imagePopupModal = new bootstrap.Modal(document.getElementById('imagePopupModal'));
            const videoPopupModal = new bootstrap.Modal(document.getElementById('videoPopupModal'));

            $('.show-detail').on('click', function() {
                const name = $(this).data('name');
                const desc = $(this).data('desc') || 'Tidak ada deskripsi.';
                const gambar = $(this).data('gambar');
                const video = $(this).data('video');
                let fitur = $(this).data('fitur');

                $('#modal-title').text('Detail: ' + name);
                $('#modal-desc').text(desc);

                // Media Handling
                const mediaContainer = $('#modal-media');
                const imagesContainer = $('#modal-images-container');
                const videosContainer = $('#modal-videos-container');

                mediaContainer.addClass('d-none');
                imagesContainer.empty();
                videosContainer.empty();

                if ((gambar && gambar.length > 0) || (video && video.length > 0)) {
                    mediaContainer.removeClass('d-none');

                    if (gambar && gambar.length > 0) {
                        gambar.forEach((src, index) => {
                            imagesContainer.append(`
                                <img src="${src}" class="img-fluid rounded cursor-pointer popup-image" 
                                     style="max-height: 120px; width: 120px; object-fit: cover;"
                                     data-index="${index}" data-all='${JSON.stringify(gambar)}'>
                            `);
                        });
                    }

                    if (video && video.length > 0) {
                        video.forEach((src, index) => {
                            videosContainer.append(`
                                <div class="position-relative">
                                    <video src="${src}" controls class="w-100 rounded" style="max-height: 200px"></video>
                                    <button type="button" class="btn btn-sm btn-primary position-absolute top-0 end-0 m-2 popup-video-btn" 
                                            data-index="${index}" data-all='${JSON.stringify(video)}'>
                                        <i class="ri-fullscreen-line"></i>
                                    </button>
                                </div>
                            `);
                        });
                    }
                }

                const fiturList = $('#modal-fitur');
                fiturList.empty();

                if (fitur && fitur.length > 0) {
                    fitur.forEach(item => {
                        fiturList.append(
                            `<li class="list-group-item py-2"><i class="ri-checkbox-circle-line text-success me-2"></i>${item}</li>`
                        );
                    });
                } else {
                    fiturList.append(
                        '<li class="list-group-item py-2 text-muted">Tidak ada fitur tambahan.</li>');
                }

                detailModal.show();
            });

            // Handle Image Popup
            $(document).on('click', '.popup-image', function() {
                const allImages = $(this).data('all');
                const selectedIndex = parseInt($(this).data('index')) || 0;

                const carouselInner = $('#carousel-images-inner');
                carouselInner.empty();

                if (allImages && Array.isArray(allImages)) {
                    allImages.forEach((src, idx) => {
                        carouselInner.append(`
                            <div class="carousel-item ${idx === selectedIndex ? 'active' : ''}">
                                <img src="${src}" class="img-fluid rounded shadow-lg mx-auto d-block">
                            </div>
                        `);
                    });

                    const controls = $(
                        '#imageCarousel .carousel-control-prev, #imageCarousel .carousel-control-next');
                    if (allImages.length <= 1) controls.addClass('d-none');
                    else controls.removeClass('d-none');

                    // Re-initialize carousel
                    const carouselEl = document.getElementById('imageCarousel');
                    const carousel = bootstrap.Carousel.getOrCreateInstance(carouselEl);
                    carousel.to(selectedIndex);
                } else {
                    const src = $(this).attr('src');
                    carouselInner.append(`
                        <div class="carousel-item active">
                            <img src="${src}" class="img-fluid rounded shadow-lg mx-auto d-block">
                        </div>
                    `);
                    $('#imageCarousel .carousel-control-prev, #imageCarousel .carousel-control-next')
                        .addClass('d-none');
                }

                imagePopupModal.show();
            });

            // Handle Video Popup from Detail Modal
            $(document).on('click', '.popup-video-btn', function() {
                const allVideos = $(this).data('all');
                const selectedIndex = parseInt($(this).data('index')) || 0;

                const carouselInner = $('#carousel-videos-inner');
                carouselInner.empty();

                if (allVideos && Array.isArray(allVideos)) {
                    allVideos.forEach((src, idx) => {
                        carouselInner.append(`
                            <div class="carousel-item ${idx === selectedIndex ? 'active' : ''}">
                                <video src="${src}" controls class="w-100 rounded shadow-lg"></video>
                            </div>
                        `);
                    });

                    const controls = $(
                        '#videoCarousel .carousel-control-prev, #videoCarousel .carousel-control-next');
                    if (allVideos.length <= 1) controls.addClass('d-none');
                    else controls.removeClass('d-none');

                    // Re-initialize carousel
                    const carouselEl = document.getElementById('videoCarousel');
                    const carousel = bootstrap.Carousel.getOrCreateInstance(carouselEl);
                    carousel.to(selectedIndex);
                } else {
                    const src = $(this).data('src');
                    carouselInner.append(`
                        <div class="carousel-item active">
                            <video src="${src}" controls class="w-100 rounded shadow-lg"></video>
                        </div>
                    `);
                    $('#videoCarousel .carousel-control-prev, #videoCarousel .carousel-control-next')
                        .addClass('d-none');
                }

                videoPopupModal.show();
            });

            // Pause video when popup is closed
            document.getElementById('videoPopupModal').addEventListener('hidden.bs.modal', function() {
                $('#carousel-videos-inner video').each(function() {
                    this.pause();
                });
            });

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
