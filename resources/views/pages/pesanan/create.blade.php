@extends('layouts/layoutMaster')

@section('title', 'Buat Pesanan Baru')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="mb-4">
            <h4 class="fw-bold mb-0">
                <span class="text-muted fw-light">Transaksi /</span> Buat Pesanan
            </h4>
        </div>

        <form action="{{ route('pesanan.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-7">
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Informasi Acara</h5>
                            <a href="{{ route('pesanan.index') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="ri-arrow-left-line me-1"></i> Kembali
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="tanggal_acara">Tanggal Acara <span
                                            class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('tanggal_acara') is-invalid @enderror"
                                        id="tanggal_acara" name="tanggal_acara" value="{{ old('tanggal_acara') }}">
                                    @error('tanggal_acara')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="waktu_mulai_acara">Waktu Mulai <span
                                            class="text-danger">*</span></label>
                                    <input type="time"
                                        class="form-control @error('waktu_mulai_acara') is-invalid @enderror"
                                        id="waktu_mulai_acara" name="waktu_mulai_acara"
                                        value="{{ old('waktu_mulai_acara') }}">
                                    @error('waktu_mulai_acara')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="lokasi_acara">Lokasi Acara <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('lokasi_acara') is-invalid @enderror"
                                    id="lokasi_acara" name="lokasi_acara" value="{{ old('lokasi_acara') }}"
                                    placeholder="Contoh: Gedung Serbaguna, Jakarta">
                                @error('lokasi_acara')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="deskripsi_acara">Deskripsi / Detail Acara</label>
                                <textarea class="form-control @error('deskripsi_acara') is-invalid @enderror" id="deskripsi_acara"
                                    name="deskripsi_acara" rows="4" placeholder="Jelaskan detail acara... Adanya permintaan khusus dll.">{{ old('deskripsi_acara') }}</textarea>
                                @error('deskripsi_acara')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-5">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Pilih Paket Layanan</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-4 pb-2 border-bottom">
                                <label class="form-label fw-bold">1. Pilih Kategori Layanan</label>
                                <select id="kategori-filter" class="form-select border-primary">
                                    <option value="">-- Pilih Kategori --</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->nama }}</option>
                                    @endforeach
                                </select>
                                <div class="form-text text-primary"><i class="ri-information-line me-1"></i>Pilih kategori
                                    terlebih dahulu untuk melihat paket yang tersedia.</div>
                            </div>

                            <div id="items-container">
                                <div class="item-row border rounded p-3 mb-3 bg-light">
                                    <div class="mb-3">
                                        <label class="form-label">Paket Layanan <span class="text-danger">*</span></label>
                                        <select name="items[0][paket_layanan_id]" class="form-select paket-select" required>
                                            <option value="">Pilih Paket</option>
                                            @foreach ($paketLayanan as $p)
                                                <option value="{{ $p->id }}" data-price="{{ $p->harga_dasar }}"
                                                    data-desc="{{ $p->deskripsi }}"
                                                    data-fitur="{{ json_encode($p->fitur) }}"
                                                    data-kategori-id="{{ $p->layanan->kategori_id ?? '' }}">
                                                    {{ $p->nama }} - Rp
                                                    {{ number_format($p->harga_dasar, 0, ',', '.') }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-3 d-none info-paket">
                                        <div class="p-2 border rounded bg-white small">
                                            <div class="fw-bold mb-1 text-primary">Detail Paket:</div>
                                            <div class="desc-paket italic mb-2"></div>
                                            <div class="fitur-paket"></div>
                                        </div>
                                    </div>
                                    <div class="mb-0">
                                        <label class="form-label">Jumlah</label>
                                        <input type="number" name="items[0][jumlah]" class="form-control jumlah-input"
                                            value="1" min="1" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="button" class="btn btn-sm btn-outline-primary mb-4 mx-5" id="add-item">
                            <i class="ri-add-line me-1"></i> Tambah Paket Lain
                        </button>

                        <div class="d-flex justify-content-between mb-2 mx-5">
                            <span>Subtotal</span>
                            <span class="fw-bold" id="subtotal-display">Rp 0</span>
                        </div>
                        <div class="d-flex justify-content-between mb-4 border-top pt-2 mx-5">
                            <span class="h5">Total Estimasi</span>
                            <span class="h5 text-primary" id="total-display">Rp 0</span>
                        </div>

                        <button type="submit" class="btn btn-primary mx-5 mb-5">Buat Pesanan Sekarang</button>
                    </div>
                </div>
            </div>
    </div>
    </form>
    </div>
@endsection

@section('page-script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('items-container');
            const addButton = document.getElementById('add-item');
            const kategoriFilter = document.getElementById('kategori-filter');
            let itemCount = 1;

            function updatePackageInfo(row) {
                const select = row.querySelector('.paket-select');
                const infoContainer = row.querySelector('.info-paket');
                const descContainer = row.querySelector('.desc-paket');
                const fiturContainer = row.querySelector('.fitur-paket');

                if (!select || !infoContainer) return;

                const selectedOption = select.options[select.selectedIndex];
                const desc = selectedOption?.dataset.desc || '';
                let fitur = [];
                try {
                    fitur = JSON.parse(selectedOption?.dataset.fitur || '[]');
                } catch (e) {
                    fitur = [];
                }

                if (select.value && (desc || fitur.length > 0)) {
                    infoContainer.classList.remove('d-none');
                    descContainer.textContent = desc || 'Tidak ada deskripsi.';

                    fiturContainer.innerHTML = '';
                    if (fitur.length > 0) {
                        const list = document.createElement('ul');
                        list.className = 'list-unstyled mb-0 mt-1';
                        fitur.forEach(item => {
                            const li = document.createElement('li');
                            li.innerHTML =
                                `<i class="ri-checkbox-circle-line text-success me-1"></i>${item}`;
                            list.appendChild(li);
                        });
                        fiturContainer.appendChild(list);
                    }
                } else {
                    infoContainer.classList.add('d-none');
                }
            }

            function calculateTotal() {
                let subtotal = 0;
                document.querySelectorAll('.item-row').forEach(row => {
                    const select = row.querySelector('.paket-select');
                    const jumlahInput = row.querySelector('.jumlah-input');
                    if (select && jumlahInput) {
                        const jumlah = parseInt(jumlahInput.value) || 0;
                        const price = parseFloat(select.options[select.selectedIndex]?.dataset.price) || 0;
                        subtotal += price * jumlah;
                    }
                });

                const formatted = new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(subtotal);

                document.getElementById('subtotal-display').innerText = formatted;
                document.getElementById('total-display').innerText = formatted;
            }

            function filterPackages() {
                const kategoriId = kategoriFilter.value;
                document.querySelectorAll('.paket-select').forEach(select => {
                    const options = select.querySelectorAll('option');
                    options.forEach(option => {
                        if (option.value === "") return;
                        const pkgKategoriId = option.dataset.kategoriId;
                        if (!kategoriId || pkgKategoriId == kategoriId) {
                            option.style.display = 'block';
                            option.disabled = false;
                        } else {
                            option.style.display = 'none';
                            option.disabled = true;
                        }
                    });

                    // Reset selected if now disabled
                    const selectedOption = select.options[select.selectedIndex];
                    if (selectedOption && selectedOption.disabled) {
                        select.value = "";
                        const row = select.closest('.item-row');
                        updatePackageInfo(row);
                    }
                });
                calculateTotal();
            }

            kategoriFilter.addEventListener('change', filterPackages);

            // Event Delegation for all dynamics
            container.addEventListener('change', function(e) {
                if (e.target.classList.contains('paket-select')) {
                    const row = e.target.closest('.item-row');
                    const selectedOption = e.target.options[e.target.selectedIndex];
                    const katId = selectedOption?.dataset.kategoriId;

                    // Auto-lock category if not set
                    if (katId && !kategoriFilter.value) {
                        kategoriFilter.value = katId;
                        filterPackages(); // This will also handle calculations
                    } else {
                        updatePackageInfo(row);
                        calculateTotal();
                    }
                }
            });

            container.addEventListener('input', function(e) {
                if (e.target.classList.contains('jumlah-input')) {
                    calculateTotal();
                }
            });

            container.addEventListener('click', function(e) {
                if (e.target.closest('.remove-item')) {
                    e.target.closest('.item-row').remove();
                    calculateTotal();
                }
            });

            addButton.addEventListener('click', function() {
                const div = document.createElement('div');
                div.className = 'item-row border rounded p-3 mb-3 bg-white position-relative';
                div.innerHTML = `
                <button type="button" class="btn btn-sm btn-icon btn-outline-danger position-absolute top-0 end-0 m-2 remove-item">
                    <i class="ri-close-line"></i>
                </button>
                <div class="mb-3">
                    <label class="form-label">Paket Layanan <span class="text-danger">*</span></label>
                    <select name="items[${itemCount}][paket_layanan_id]" class="form-select paket-select" required>
                        <option value="">Pilih Paket</option>
                        @foreach ($paketLayanan as $p)
                            <option value="{{ $p->id }}" data-price="{{ $p->harga_dasar }}"
                                data-desc="{{ $p->deskripsi }}"
                                data-fitur="{{ json_encode($p->fitur) }}"
                                data-kategori-id="{{ $p->layanan->kategori_id ?? '' }}">
                                {{ $p->nama }} - Rp {{ number_format($p->harga_dasar, 0, ',', '.') }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3 d-none info-paket">
                    <div class="p-2 border rounded bg-white small">
                        <div class="fw-bold mb-1 text-primary">Detail Paket:</div>
                        <div class="desc-paket italic mb-2"></div>
                        <div class="fitur-paket"></div>
                    </div>
                </div>
                <div class="mb-0">
                    <label class="form-label">Jumlah</label>
                    <input type="number" name="items[${itemCount}][jumlah]" class="form-control jumlah-input" value="1" min="1" required>
                </div>
            `;
                container.appendChild(div);
                itemCount++;

                // Apply current filter to new dropdown
                const kategoriId = kategoriFilter.value;
                if (kategoriId) {
                    const newSelect = div.querySelector('.paket-select');
                    newSelect.querySelectorAll('option').forEach(option => {
                        if (option.value === "") return;
                        if (option.dataset.kategoriId != kategoriId) {
                            option.style.display = 'none';
                            option.disabled = true;
                        }
                    });
                }
            });

            // PRE-SELECT LOGIC
            const selectedPaketId = "{{ $selectedPaketId ?? '' }}";
            if (selectedPaketId) {
                const select = document.querySelector('.paket-select');
                if (select) {
                    const option = select.querySelector(`option[value="${selectedPaketId}"]`);
                    if (option) {
                        const katId = option.dataset.kategoriId;
                        if (katId) {
                            kategoriFilter.value = katId;
                            filterPackages();
                        }
                        select.value = selectedPaketId;
                        updatePackageInfo(select.closest('.item-row'));
                        calculateTotal();
                    }
                }
            } else {
                calculateTotal(); // Initial calc
            }
        });
    </script>
@endsection
