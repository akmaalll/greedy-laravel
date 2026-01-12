@extends('layouts/layoutMaster')

@section('title', 'Detail Pembayaran')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">
            <span class="text-muted fw-light">Transaksi /</span> Detail Pembayaran
        </h4>
        <div class="d-flex gap-2">
            @if(auth()->user()->role->slug == 'admin')
            <a href="{{ route('pembayaran.edit', $data->id) }}" class="btn btn-warning">
                <i class="ri-edit-line me-1"></i> Edit / Verifikasi
            </a>
            @endif
            <a href="{{ route('pembayaran.index') }}" class="btn btn-outline-secondary">
                <i class="ri-arrow-left-line me-1"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header border-bottom">
                    <h5 class="mb-0">Rincian Transaksi</h5>
                </div>
                <div class="card-body pt-4">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-1">No. Pesanan:</h6>
                            <p class="h6 text-primary">{{ $data->pesanan->nomor_pesanan }}</p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <h6 class="text-muted mb-1">Tanggal Transaksi:</h6>
                             <p class="h6">{{ \Carbon\Carbon::parse($data->waktu_bayar)->format('d F Y') }}</p>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-1">Metode Pembayaran:</h6>
                            <p class="h6"><i class="ri-bank-card-line me-1"></i> {{ strtoupper($data->metode_pembayaran) }}</p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <h6 class="text-muted mb-1">Status:</h6>
                            @php
                                $statusClasses = [
                                    'menunggu' => 'bg-label-warning',
                                    'lunas' => 'bg-label-success',
                                    'gagal' => 'bg-label-danger'
                                ];
                                $class = $statusClasses[$data->status] ?? 'bg-label-secondary';
                            @endphp
                            <span class="badge {{ $class }} h5">{{ strtoupper($data->status) }}</span>
                            
                            @if(auth()->user()->role->slug == 'admin' && $data->status == 'menunggu')
                            <div class="mt-2">
                                <form action="{{ route('pembayaran.update', $data->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="pesanan_id" value="{{ $data->pesanan_id }}">
                                    <input type="hidden" name="status" value="lunas">
                                    <input type="hidden" name="jumlah" value="{{ $data->jumlah }}">
                                    <input type="hidden" name="metode_pembayaran" value="{{ $data->metode_pembayaran }}">
                                    <button type="submit" class="btn btn-sm btn-success">
                                        <i class="ri-check-line me-1"></i> Verifikasi Lunas Sekarang
                                    </button>
                                </form>
                            </div>
                            @endif
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between align-items-center py-3">
                        <span class="h5 mb-0">Total Pembayaran:</span>
                        <span class="h4 text-primary mb-0">Rp {{ number_format($data->jumlah, 0, ',', '.') }}</span>
                    </div>

                    <div class="mt-3">
                        <h6 class="text-muted mb-2">Catatan:</h6>
                        <div class="p-3 bg-light rounded italic">
                            {{ $data->catatan ?: 'Tidak ada catatan.' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4 text-center">
                <div class="card-header border-bottom">
                    <h5 class="mb-0">Bukti Pembayaran</h5>
                </div>
                <div class="card-body pt-4">
                    @if($data->bukti_gambar)
                        <img src="{{ $data->bukti_gambar }}" alt="Bukti Pembayaran" class="img-fluid rounded border mb-3">
                        <a href="{{ $data->bukti_gambar }}" target="_blank" class="btn btn-sm btn-outline-primary">Lihat Ukuran Penuh</a>
                    @else
                        <div class="py-5 bg-light rounded border text-muted">
                            <i class="ri-image-line ri-3x d-block mb-2"></i>
                            Tidak ada bukti pembayaran diunggah.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
