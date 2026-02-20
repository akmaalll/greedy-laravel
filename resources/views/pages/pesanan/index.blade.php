@extends('layouts/layoutMaster')

@section('title', 'Daftar Pesanan')

@section('vendor-style')
    @vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss'])
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js'])
@endsection

@if (!auth()->check())
    <style>
        @media (max-width: 575px) {

            .layout-menu-toggle,
            .navbar-nav-right,
            .dropdown-user,
            .dropdown-menu.dropdown-menu-end.show {
                display: none !important;
            }
        }
    </style>
@endif

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        {{-- PUBLIC / CLIENT VIEW --}}
        @if (!auth()->check() || auth()->user()->role->slug == 'client')

            {{-- CATALOG SECTION (Visible to guests and clients) --}}
            <!-- Katalog Pesanan (Header & Filters) -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
                <h4 class="fw-bold mb-0">Pilih Layanan Kami</h4>

                <div class="btn-group" role="group" aria-label="Filter Kategori">
                    <button type="button" class="btn btn-primary active filter-btn" data-filter="all">Semua</button>
                    @foreach ($categories as $cat)
                        <button type="button" class="btn btn-outline-primary filter-btn" data-filter="{{ $cat->id }}">
                            {{ $cat->nama }}
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Service Catalog -->
            <div class="row mb-6" id="catalog-container">
                @foreach ($paketLayanan as $paket)
                    <div class="col-md-6 col-lg-3 mb-4 package-item" data-category-id="{{ $paket->layanan->kategori_id }}">
                        <div class="card h-100 shadow-sm border-0 overflow-hidden">
                            {{-- Package Media Header (Carousel) --}}
                            <div class="position-relative" style="height: 200px;">
                                <div id="cardCarousel-{{ $paket->id }}" class="carousel slide h-100"
                                    data-bs-ride="carousel">
                                    <div class="carousel-inner h-100">
                                        @php
                                            $allMedia = is_array($paket->gambar)
                                                ? $paket->gambar
                                                : ($paket->gambar
                                                    ? [$paket->gambar]
                                                    : []);
                                            $videoPath = is_array($paket->video)
                                                ? $paket->video[0] ?? null
                                                : $paket->video;
                                        @endphp

                                        @forelse($allMedia as $index => $img)
                                            <div class="carousel-item h-100 {{ $index === 0 ? 'active' : '' }}">
                                                <img src="{{ asset('storage/' . $img) }}"
                                                    class="w-100 h-100 object-fit-cover cursor-pointer popup-image"
                                                    data-index="{{ $index }}" data-all='@json(array_map(fn($g) => asset('storage/' . $g), $allMedia))'
                                                    alt="{{ $paket->nama }}">
                                            </div>
                                        @empty
                                            <div class="carousel-item active h-100">
                                                <div
                                                    class="w-100 h-100 bg-label-primary d-flex align-items-center justify-content-center">
                                                    <i class="ri-camera-lens-line ri-64px opacity-25"></i>
                                                </div>
                                            </div>
                                        @endforelse
                                    </div>
                                    @if (count($allMedia) > 1)
                                        <button class="carousel-control-prev" type="button"
                                            data-bs-target="#cardCarousel-{{ $paket->id }}" data-bs-slide="prev"
                                            style="width: 30px; background: transparent; border: 0;">
                                            <span class="carousel-control-prev-icon" aria-hidden="true"
                                                style="width: 1.5rem; height: 1.5rem;"></span>
                                        </button>
                                        <button class="carousel-control-next" type="button"
                                            data-bs-target="#cardCarousel-{{ $paket->id }}" data-bs-slide="next"
                                            style="width: 30px; background: transparent; border: 0;">
                                            <span class="carousel-control-next-icon" aria-hidden="true"
                                                style="width: 1.5rem; height: 1.5rem;"></span>
                                        </button>
                                    @endif
                                </div>

                                {{-- Overlays --}}
                                <div class="position-absolute top-0 start-0 m-3" style="z-index: 5;">
                                    <span
                                        class="badge bg-primary shadow-sm">{{ $paket->layanan->kategori->nama ?? 'Umum' }}</span>
                                </div>

                                @if ($videoPath)
                                    <div class="position-absolute bottom-0 end-0 m-3" style="z-index: 5;">
                                        <button type="button"
                                            class="btn btn-sm btn-dark rounded-pill btn-icon shadow-sm popup-video-trigger"
                                            data-index="0" data-all='@json(is_array($paket->video)
                                                    ? array_map(fn($v) => asset('storage/' . $v), $paket->video)
                                                    : [$paket->video ? asset('storage/' . $paket->video) : '']
                                            )'>
                                            <i class="ri-play-fill ri-20px"></i>
                                        </button>
                                    </div>
                                @endif
                            </div>

                            <div class="card-body p-4 d-flex flex-column">
                                <div class="mb-3">
                                    <h5 class="card-title fw-bold mb-1">{{ $paket->nama }}</h5>
                                    <small class="text-muted d-block"><i
                                            class="ri-settings-4-line me-1"></i>{{ $paket->layanan->nama ?? '-' }}</small>
                                </div>

                                <div class="mb-3">
                                    <h4 class="text-primary fw-bold mb-0">
                                        <small class="fs-6 fw-normal text-muted">Mulai</small>
                                        Rp {{ number_format($paket->harga_dasar, 0, ',', '.') }}
                                    </h4>
                                </div>

                                <div class="features-list mb-4 flex-grow-1">
                                    <label class="small fw-bold text-muted text-uppercase mb-2 d-block">Detail
                                        Paket:</label>
                                    <ul class="list-unstyled mb-0">
                                        @php
                                            $features = is_array($paket->fitur)
                                                ? $paket->fitur
                                                : ($paket->fitur
                                                    ? explode(',', $paket->fitur)
                                                    : []);
                                        @endphp
                                        @forelse(array_slice($features, 0, 5) as $f)
                                            <li class="small mb-2 d-flex align-items-start text-muted">
                                                <i class="ri-checkbox-circle-fill text-success me-2 mt-1"></i>
                                                <span>{{ trim($f) }}</span>
                                            </li>
                                        @empty
                                            <li class="small text-muted italic">Lihat detail untuk informasi lebih lanjut.
                                            </li>
                                        @endforelse
                                        @if (count($features) > 5)
                                            <li class="small text-primary mt-1">+ {{ count($features) - 5 }} fitur
                                                lainnya
                                            </li>
                                        @endif
                                    </ul>
                                </div>

                                <div class="mt-auto pt-3 border-top">
                                    @if (auth()->check())
                                        <a href="{{ route('pesanan.create', ['selected_paket' => $paket->id]) }}"
                                            class="btn btn-primary w-100 fw-bold">Pesan Sekarang</a>
                                    @else
                                        <a href="{{ route('login', ['selected_paket' => $paket->id]) }}"
                                            class="btn btn-primary w-100 fw-bold guest-pesan">Pesan Sekarang</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const buttons = document.querySelectorAll('.filter-btn');
                    const items = document.querySelectorAll('.package-item');

                    buttons.forEach(btn => {
                        btn.addEventListener('click', function() {
                            // Activate button
                            buttons.forEach(b => {
                                b.classList.remove('btn-primary', 'active');
                                b.classList.add('btn-outline-primary');
                            });
                            this.classList.remove('btn-outline-primary');
                            this.classList.add('btn-primary', 'active');

                            const filter = this.getAttribute('data-filter');

                            // Filter items
                            items.forEach(item => {
                                if (filter === 'all' || item.getAttribute('data-category-id') ==
                                    filter) {
                                    item.style.display = 'block';
                                    item.classList.add('animate__animated', 'animate__fadeIn');
                                } else {
                                    item.style.display = 'none';
                                    item.classList.remove('animate__animated', 'animate__fadeIn');
                                }
                            });
                        });
                    });
                });
            </script>

            {{-- CLIENT HISTORY SECTION (Only for logged in clients) --}}
            @if (auth()->check() && auth()->user()->role->slug == 'client')
                <!-- History Orders Section -->
                <div class="card mb-6">
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
                                                <span
                                                    class="fw-semibold">{{ $item->tanggal_acara ? \Carbon\Carbon::parse($item->tanggal_acara)->format('d M Y') : '-' }}</span>
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
                                                    'dibatalkan' => 'danger',
                                                ];
                                                $color = $statusColors[$item->status] ?? 'secondary';
                                            @endphp
                                            <span class="badge bg-label-{{ $color }} text-uppercase">
                                                {{ $item->status }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="fw-bold">Rp
                                                {{ number_format($item->total_harga, 0, ',', '.') }}</span>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <a href="{{ route('pesanan.show', $item->id) }}"
                                                    class="btn btn-sm btn-outline-info" title="Detail">
                                                    <i class="ri-eye-line"></i>
                                                </a>
                                                @if ($item->status == 'menunggu')
                                                    <a href="{{ route('pesanan.edit', $item->id) }}"
                                                        class="btn btn-sm btn-outline-primary" title="Edit">
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
            @endif
        @elseif(auth()->check())
            {{-- ADMIN & PHOTOGRAPHER VIEW --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold mb-0">
                    <span class="text-muted fw-light">Transaksi /</span> Daftar Pesanan
                </h4>
                @if (auth()->check() && auth()->user()->role->slug == 'admin')
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
                                                'dibatalkan' => 'bg-label-danger',
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
                                            <a href="{{ route('pesanan.show', $item->id) }}"
                                                class="btn btn-sm btn-outline-info" title="Detail">
                                                <i class="ri-eye-line"></i>
                                            </a>
                                            @if (auth()->check() && auth()->user()->role->slug == 'admin')
                                                <a href="{{ route('pesanan.edit', $item->id) }}"
                                                    class="btn btn-sm btn-outline-primary" title="Edit">
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

        .object-fit-cover {
            object-fit: cover;
        }

        .text-truncate-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .package-item .card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .package-item .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
        }

        .features-list ul li i {
            font-size: 1.1rem;
        }

        /* Main Popup Carousel Controls */
        #imagePopupModal .carousel-control-prev,
        #imagePopupModal .carousel-control-next,
        #videoPopupModal .carousel-control-prev,
        #videoPopupModal .carousel-control-next {
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

        #imagePopupModal .carousel-control-prev:hover,
        #imagePopupModal .carousel-control-next:hover,
        #videoPopupModal .carousel-control-prev:hover,
        #videoPopupModal .carousel-control-next:hover {
            background: rgba(0, 0, 0, 0.8);
            opacity: 1;
        }

        /* Card Header Carousel Controls */
        .package-item .carousel-control-prev,
        .package-item .carousel-control-next {
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .package-item .card:hover .carousel-control-prev,
        .package-item .card:hover .carousel-control-next {
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
            const imagePopupModal = new bootstrap.Modal(document.getElementById('imagePopupModal'));
            const videoPopupModal = new bootstrap.Modal(document.getElementById('videoPopupModal'));

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

                    // Re-initialize carousel to ensure it works with new content
                    const carouselEl = document.getElementById('imageCarousel');
                    const carousel = bootstrap.Carousel.getOrCreateInstance(carouselEl);
                    carousel.to(selectedIndex);
                }

                imagePopupModal.show();
            });

            // Handle Video Popup
            $(document).on('click', '.popup-video-trigger', function() {
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
                }

                videoPopupModal.show();
            });

            // Pause video when popup is closed
            document.getElementById('videoPopupModal').addEventListener('hidden.bs.modal', function() {
                $('#carousel-videos-inner video').each(function() {
                    this.pause();
                });
            });
            // Initialize DataTables
            const tableClient = $('#table-pesanan-client');
            if (tableClient.length) {
                tableClient.DataTable({
                    responsive: true,
                    order: [
                        [1, 'desc']
                    ], // Order by date
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
                    order: [
                        [2, 'desc']
                    ], // Order by date column
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

            // For guests: intercept "Pesan Sekarang" and show confirm before redirecting to login
            document.querySelectorAll('.guest-pesan').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    var href = this.getAttribute('href');

                    if (window.AlertHandler && typeof window.AlertHandler.confirm === 'function') {
                        window.AlertHandler.confirm(
                            'Perlu Login',
                            'Anda harus login untuk melakukan pemesanan. Lanjut ke halaman login?',
                            'Ya, Login',
                            function() {
                                window.location.href = href;
                            }
                        );
                    } else {
                        if (confirm(
                                'Anda harus login untuk melakukan pemesanan. Lanjut ke halaman login?'
                            )) {
                            window.location.href = href;
                        }
                    }
                });
            });
        });
    </script>
@endsection
