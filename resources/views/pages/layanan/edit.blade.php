@extends('layouts/layoutMaster')

@section('title', 'Edit Layanan')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="mb-4">
        <h4 class="fw-bold mb-0">
            <span class="text-muted fw-light">Master Data /</span> Edit Layanan
        </h4>
    </div>

    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Form Edit Layanan</h5>
                    <a href="{{ route('layanan.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="ri-arrow-left-line me-1"></i> Kembali
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('layanan.update', $data->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label" for="kategori_id">Kategori <span class="text-danger">*</span></label>
                                <select class="form-select @error('kategori_id') is-invalid @enderror" id="kategori_id" name="kategori_id">
                                    <option value="">Pilih Kategori</option>
                                    @foreach($kategori as $item)
                                        <option value="{{ $item->id }}" {{ old('kategori_id', $data->kategori_id) == $item->id ? 'selected' : '' }}>
                                            {{ $item->nama }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('kategori_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label" for="nama">Nama Layanan <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama" name="nama" value="{{ old('nama', $data->nama) }}" placeholder="Contoh: Paket Fotografi Premium">
                                @error('nama')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label">Tipe Layanan <span class="text-danger">*</span></label>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check custom-option custom-option-basic {{ old('tipe', $data->tipe) == 'fotografi' ? 'checked' : '' }}">
                                            <label class="form-check-label custom-option-content" for="tipe_fotografi">
                                                <input class="form-check-input" type="radio" name="tipe" value="fotografi" id="tipe_fotografi" {{ old('tipe', $data->tipe) == 'fotografi' ? 'checked' : '' }}>
                                                <span class="custom-option-header">
                                                    <span class="h6 mb-0"><i class="ri-camera-line me-2"></i>Fotografi</span>
                                                </span>
                                                <span class="custom-option-body">
                                                    <small>Layanan foto untuk acara</small>
                                                </span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check custom-option custom-option-basic {{ old('tipe', $data->tipe) == 'videografi' ? 'checked' : '' }}">
                                            <label class="form-check-label custom-option-content" for="tipe_videografi">
                                                <input class="form-check-input" type="radio" name="tipe" value="videografi" id="tipe_videografi" {{ old('tipe', $data->tipe) == 'videografi' ? 'checked' : '' }}>
                                                <span class="custom-option-header">
                                                    <span class="h6 mb-0"><i class="ri-video-line me-2"></i>Videografi</span>
                                                </span>
                                                <span class="custom-option-body">
                                                    <small>Layanan video untuk acara</small>
                                                </span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                @error('tipe')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label" for="deskripsi">Deskripsi</label>
                                <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="3" placeholder="Jelaskan detail layanan...">{{ old('deskripsi', $data->deskripsi) }}</textarea>
                                @error('deskripsi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" id="is_aktif" name="is_aktif" value="1" {{ old('is_aktif', $data->is_aktif) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_aktif">Aktifkan Layanan</label>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary me-2">Update Layanan</button>
                            <a href="{{ route('layanan.index') }}" class="btn btn-label-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
