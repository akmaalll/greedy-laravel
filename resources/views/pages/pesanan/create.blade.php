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
                            <div id="items-container">
                                <div class="item-row border rounded p-3 mb-3 bg-light">
                                    <div class="mb-3">
                                        <label class="form-label">Paket Layanan <span class="text-danger">*</span></label>
                                        <select name="items[0][paket_layanan_id]" class="form-select paket-select" required>
                                            <option value="">Pilih Paket</option>
                                            @foreach ($paketLayanan as $p)
                                                <option value="{{ $p->id }}" data-price="{{ $p->harga_dasar }}">
                                                    {{ $p->nama }} - Rp
                                                    {{ number_format($p->harga_dasar, 0, ',', '.') }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-0">
                                        <label class="form-label">Jumlah</label>
                                        <input type="number" name="items[0][jumlah]" class="form-control jumlah-input"
                                            value="1" min="1" required>
                                    </div>
                                </div>
                            </div>

                            <button type="button" class="btn btn-sm btn-outline-primary mb-4" id="add-item">
                                <i class="ri-add-line me-1"></i> Tambah Paket Lain
                            </button>

                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal</span>
                                <span class="fw-bold" id="subtotal-display">Rp 0</span>
                            </div>
                            <div class="d-flex justify-content-between mb-4 border-top pt-2">
                                <span class="h5">Total Estimasi</span>
                                <span class="h5 text-primary" id="total-display">Rp 0</span>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Buat Pesanan Sekarang</button>
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
            let itemCount = 1;

            // PRE-SELECT LOGIC
            const selectedPaketId = "{{ $selectedPaketId ?? '' }}";
            if (selectedPaketId) {
                const select = document.querySelector('.paket-select');
                if (select) {
                    select.value = selectedPaketId;
                    // Dispatch change event to trigger calculation
                    select.dispatchEvent(new Event('change', {
                        bubbles: true
                    }));
                }
            }

            function calculateTotal() {
                let subtotal = 0;
                document.querySelectorAll('.item-row').forEach(row => {
                    const select = row.querySelector('.paket-select');
                    const jumlah = row.querySelector('.jumlah-input').value;
                    const price = select.options[select.selectedIndex]?.dataset.price || 0;
                    subtotal += parseFloat(price) * parseInt(jumlah);
                });

                const formatted = new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(subtotal);
                document.getElementById('subtotal-display').innerText = formatted;
                document.getElementById('total-display').innerText = formatted;
            }

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
                            <option value="{{ $p->id }}" data-price="{{ $p->harga_dasar }}">
                                {{ $p->nama }} - Rp {{ number_format($p->harga_dasar, 0, ',', '.') }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-0">
                    <label class="form-label">Jumlah</label>
                    <input type="number" name="items[${itemCount}][jumlah]" class="form-control jumlah-input" value="1" min="1" required>
                </div>
            `;
                container.appendChild(div);
                itemCount++;

                // Add listeners to new inputs
                div.querySelector('.paket-select').addEventListener('change', calculateTotal);
                div.querySelector('.jumlah-input').addEventListener('input', calculateTotal);
                div.querySelector('.remove-item').addEventListener('click', function() {
                    div.remove();
                    calculateTotal();
                });
            });

            container.addEventListener('change', function(e) {
                if (e.target.classList.contains('paket-select')) calculateTotal();
            });

            container.addEventListener('input', function(e) {
                if (e.target.classList.contains('jumlah-input')) calculateTotal();
            });
        });
    </script>
@endsection
