@extends('layouts/layoutMaster')

@section('title', 'Catat Pembayaran')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="mb-4">
        <h4 class="fw-bold mb-0">
            <span class="text-muted fw-light">Transaksi /</span> Catat Pembayaran
        </h4>
    </div>

    <div class="row">
        <div class="col-md-6 mx-auto">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Form Pembayaran Baru</h5>
                    <a href="{{ route('pembayaran.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="ri-arrow-left-line me-1"></i> Kembali
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('pembayaran.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Nomor Pesanan</label>
                            <input type="text" class="form-control bg-light" value="{{ $pesanan->nomor_pesanan ?? 'Pesanan Not Found' }}" readonly>
                            <input type="hidden" name="pesanan_id" value="{{ $pesananId }}">
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Bayar <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('waktu_bayar') is-invalid @enderror" name="waktu_bayar" value="{{ date('Y-m-d') }}">
                                @error('waktu_bayar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jumlah Bayar <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    @php
                                        $terbayar = $pesanan ? $pesanan->pembayaran->where('status', 'lunas')->sum('jumlah') : 0;
                                        $sisa = $pesanan ? ($pesanan->total_harga - $terbayar) : 0;
                                    @endphp
                                    <input type="number" class="form-control @error('jumlah') is-invalid @enderror" name="jumlah" value="{{ old('jumlah', $sisa) }}">
                                </div>
                                <small class="text-muted">Sisa Tagihan: Rp {{ number_format($sisa, 0, ',', '.') }}</small>
                                @error('jumlah')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Metode Pembayaran <span class="text-danger">*</span></label>
                            <select class="form-select @error('metode_pembayaran') is-invalid @enderror" name="metode_pembayaran">
                                <option value="transfer">Transfer Bank</option>
                                <option value="ewallet">E-Wallet (Dana/OVO/Gopay)</option>
                                @if(auth()->user()->role->slug == 'admin')
                                <option value="tunai">Tunai</option>
                                @endif
                            </select>
                            @error('metode_pembayaran')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        @if(auth()->user()->role->slug == 'admin')
                        <div class="mb-3">
                            <label class="form-label">Status Pembayaran <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" name="status">
                                <option value="menunggu">Pending / Menunggu Verifikasi</option>
                                <option value="lunas">Lunas</option>
                                <option value="gagal">Gagal / Ditolak</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        @endif

                        {{-- <div class="mb-3">
                            <label class="form-label">Catatan / Keterangan</label>
                            <textarea class="form-control" name="catatan" rows="2" placeholder="Contoh: Bukti transfer sudah dikirim via WhatsApp">{{ old('catatan') }}</textarea>
                        </div> --}}

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="ri-check-line me-1"></i> {{ auth()->user()->role->slug == 'client' ? 'Kirim Konfirmasi Pembayaran' : 'Simpan Data Pembayaran' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
