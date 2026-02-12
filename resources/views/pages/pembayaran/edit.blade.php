@extends('layouts/layoutMaster')

@section('title', 'Edit Pembayaran')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="mb-4">
        <h4 class="fw-bold mb-0">
            <span class="text-muted fw-light">Transaksi /</span> Edit Pembayaran #{{ $data->id }}
        </h4>
    </div>

    <form action="{{ route('pembayaran.update', $data->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Informasi Pembayaran</h5>
                        <a href="{{ route('pembayaran.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="ri-arrow-left-line me-1"></i> Kembali
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Pesanan</label>
                            <input type="text" class="form-control" value="{{ $data->pesanan->nomor_pesanan }} - {{ $data->pesanan->klien->name }}" readonly disabled>
                            <input type="hidden" name="pesanan_id" value="{{ $data->pesanan_id }}">
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Waktu Bayar <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control @error('waktu_bayar') is-invalid @enderror" name="waktu_bayar" value="{{ old('waktu_bayar', $data->waktu_bayar ? $data->waktu_bayar->format('Y-m-d\TH:i') : '') }}">
                                @error('waktu_bayar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jumlah <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control @error('jumlah') is-invalid @enderror" name="jumlah" value="{{ old('jumlah', (int)$data->jumlah) }}">
                                </div>
                                @error('jumlah')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Metode Pembayaran <span class="text-danger">*</span></label>
                            <select class="form-select @error('metode_pembayaran') is-invalid @enderror" name="metode_pembayaran">
                                <option value="transfer" {{ old('metode_pembayaran', $data->metode_pembayaran) == 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
                                <option value="ewallet" {{ old('metode_pembayaran', $data->metode_pembayaran) == 'ewallet' ? 'selected' : '' }}>E-Wallet</option>
                                <option value="tunai" {{ old('metode_pembayaran', $data->metode_pembayaran) == 'tunai' ? 'selected' : '' }}>Tunai</option>
                            </select>
                            @error('metode_pembayaran')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" name="status">
                                <option value="menunggu" {{ old('status', $data->status) == 'menunggu' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                                <option value="lunas" {{ old('status', $data->status) == 'lunas' ? 'selected' : '' }}>Lunas</option>
                                <option value="gagal" {{ old('status', $data->status) == 'gagal' ? 'selected' : '' }}>Gagal / Ditolak</option>
                                <option value="dikembalikan" {{ old('status', $data->status) == 'dikembalikan' ? 'selected' : '' }}>Dikembalikan</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
{{-- 
                        <div class="mb-3">
                            <label class="form-label">Catatan</label>
                            <textarea class="form-control @error('catatan') is-invalid @enderror" name="catatan" rows="3">{{ old('catatan', $data->catatan) }}</textarea>
                            @error('catatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div> --}}
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header border-bottom">
                        <h5 class="mb-0">Aksi</h5>
                    </div>
                    <div class="card-body pt-4">
                        <button type="submit" class="btn btn-primary w-100 mb-2">Simpan Perubahan</button>
                        <a href="{{ route('pembayaran.show', $data->id) }}" class="btn btn-outline-secondary w-100">Batal</a>
                    </div>
                </div>

                {{-- <div class="card text-center">
                    <div class="card-header border-bottom">
                        <h5 class="mb-0">Bukti Gambar</h5>
                    </div>
                    <div class="card-body pt-4">
                        @if($data->bukti_gambar)
                            <img src="{{ $data->bukti_gambar }}" class="img-fluid rounded border mb-2">
                        @else
                            <p class="text-muted">Tidak ada bukti gambar.</p>
                        @endif
                        <input type="text" class="form-control form-control-sm" name="bukti_gambar" value="{{ old('bukti_gambar', $data->bukti_gambar) }}" placeholder="URL Bukti Gambar">
                    </div>
                </div> --}}
            </div>
        </div>
    </form>
</div>
@endsection
