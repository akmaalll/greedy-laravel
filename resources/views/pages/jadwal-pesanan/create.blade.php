@extends('layouts/layoutMaster')

@section('title', 'Tambah Agenda Detail')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="mb-4">
        <h4 class="fw-bold mb-0">
            <span class="text-muted fw-light">Operasional /</span> Tambah Agenda
        </h4>
    </div>

    <div class="row">
        <div class="col-md-6 mx-auto">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Form Kegiatan Acara</h5>
                    <a href="{{ route('jadwal-pesanan.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="ri-arrow-left-line me-1"></i> Kembali
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('jadwal-pesanan.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">ID Pesanan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="pesanan_id" value="{{ $pesananId ?? old('pesanan_id') }}">
                            @error('pesanan_id')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nama Kegiatan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('kegiatan') is-invalid @enderror" name="kegiatan" value="{{ old('kegiatan') }}" placeholder="Contoh: Sesi Foto Keluarga">
                            @error('kegiatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('tanggal') is-invalid @enderror" name="tanggal" value="{{ old('tanggal') }}">
                            @error('tanggal')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Waktu Mulai <span class="text-danger">*</span></label>
                                <input type="time" class="form-control @error('waktu_mulai') is-invalid @enderror" name="waktu_mulai" value="{{ old('waktu_mulai') }}">
                                @error('waktu_mulai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Waktu Selesai <span class="text-danger">*</span></label>
                                <input type="time" class="form-control @error('waktu_selesai') is-invalid @enderror" name="waktu_selesai" value="{{ old('waktu_selesai') }}">
                                @error('waktu_selesai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Deskripsi / Detail Kegiatan</label>
                            <textarea class="form-control" name="deskripsi" rows="3" placeholder="...">{{ old('deskripsi') }}</textarea>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary w-100">Simpan Agenda</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
