@extends('layouts/layoutMaster')

@section('title', 'Edit Ketersediaan')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="mb-4">
            <h4 class="fw-bold mb-0">
                <span class="text-muted fw-light">Penjadwalan /</span> Edit Ketersediaan
            </h4>
        </div>

        <div class="row">
            <div class="col-md-6 mx-auto">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Form Edit Jadwal</h5>
                        <a href="{{ route('ketersediaan.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="ri-arrow-left-line me-1"></i> Kembali
                        </a>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('ketersediaan.update', $data->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            @if (auth()->user()->role->slug == 'admin')
                                <div class="mb-3">
                                    <label class="form-label" for="fotografer_id">Fotografer <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select select2 @error('fotografer_id') is-invalid @enderror"
                                        id="fotografer_id" name="fotografer_id">
                                        @foreach ($photographers as $p)
                                            <option value="{{ $p->id }}"
                                                {{ old('fotografer_id', $data->fotografer_id) == $p->id ? 'selected' : '' }}>
                                                {{ $p->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('fotografer_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endif

                            <div class="mb-3">
                                <label class="form-label" for="tanggal">Tanggal <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('tanggal') is-invalid @enderror"
                                    id="tanggal" name="tanggal"
                                    value="{{ old('tanggal', $data->tanggal->format('Y-m-d')) }}">
                                @error('tanggal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="waktu_mulai">Waktu Mulai <span
                                            class="text-danger">*</span></label>`
                                    <input type="time" class="form-control @error('waktu_mulai') is-invalid @enderror"
                                        id="waktu_mulai" name="waktu_mulai"
                                        value="{{ old('waktu_mulai', $data->waktu_mulai) }}">
                                    @error('waktu_mulai')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="waktu_selesai">Waktu Selesai <span
                                            class="text-danger">*</span></label>
                                    <input type="time" class="form-control @error('waktu_selesai') is-invalid @enderror"
                                        id="waktu_selesai" name="waktu_selesai"
                                        value="{{ old('waktu_selesai', $data->waktu_selesai) }}">
                                    @error('waktu_selesai')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="status">Status <span class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status"
                                    name="status">
                                    <option value="tersedia"
                                        {{ old('status', $data->status) == 'tersedia' ? 'selected' : '' }}>Tersedia
                                    </option>
                                    <option value="tidak_tersedia"
                                        {{ old('status', $data->status) == 'tidak_tersedia' ? 'selected' : '' }}>Tidak
                                        Tersedia / Libur</option>
                                    <option value="dipesan"
                                        {{ old('status', $data->status) == 'dipesan' ? 'selected' : '' }} disabled>Dipesan
                                        (Auto)</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="catatan">Catatan / Alasan</label>
                                <textarea class="form-control @error('catatan') is-invalid @enderror" id="catatan" name="catatan" rows="3"
                                    placeholder="...">{{ old('catatan', $data->catatan) }}</textarea>
                                @error('catatan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary me-2 w-100">Update Jadwal</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
