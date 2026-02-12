@extends('layouts/layoutMaster')

@section('title', 'Edit Paket Layanan')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="mb-4">
            <h4 class="fw-bold mb-0">
                <span class="text-muted fw-light">Master Data /</span> Edit Paket Layanan
            </h4>
        </div>

        <div class="row">
            <div class="col-md-10 mx-auto">
                <form action="{{ route('paket-layanan.update', $data->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-7">
                            <div class="card mb-4">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Informasi Dasar Paket</h5>
                                    <a href="{{ route('paket-layanan.index') }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="ri-arrow-left-line me-1"></i> Kembali
                                    </a>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label" for="layanan_id">Layanan <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select @error('layanan_id') is-invalid @enderror"
                                            id="layanan_id" name="layanan_id">
                                            <option value="">Pilih Layanan</option>
                                            @foreach ($layanan as $item)
                                                <option value="{{ $item->id }}"
                                                    {{ old('layanan_id', $data->layanan_id) == $item->id ? 'selected' : '' }}>
                                                    {{ $item->nama }} ({{ $item->kategori->nama ?? '-' }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('layanan_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label" for="nama">Nama Paket <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('nama') is-invalid @enderror"
                                            id="nama" name="nama" value="{{ old('nama', $data->nama) }}"
                                            placeholder="Contoh: Paket Wedding Silver">
                                        @error('nama')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label" for="deskripsi">Deskripsi</label>
                                        <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="3"
                                            placeholder="Jelaskan detail paket...">{{ old('deskripsi', $data->deskripsi) }}</textarea>
                                        @error('deskripsi')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label" for="tipe_durasi">Tipe Durasi <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-select @error('tipe_durasi') is-invalid @enderror"
                                                id="tipe_durasi" name="tipe_durasi">
                                                <option value="jam"
                                                    {{ old('tipe_durasi', $data->tipe_durasi) == 'jam' ? 'selected' : '' }}>
                                                    Jam</option>
                                                <option value="foto"
                                                    {{ old('tipe_durasi', $data->tipe_durasi) == 'foto' ? 'selected' : '' }}>
                                                    Foto / Lembar</option>
                                                <option value="menit"
                                                    {{ old('tipe_durasi', $data->tipe_durasi) == 'menit' ? 'selected' : '' }}>
                                                    Menit</option>
                                            </select>
                                            @error('tipe_durasi')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label" for="nilai_durasi">Nilai Durasi <span
                                                    class="text-danger">*</span></label>
                                            <input type="number"
                                                class="form-control @error('nilai_durasi') is-invalid @enderror"
                                                id="nilai_durasi" name="nilai_durasi"
                                                value="{{ old('nilai_durasi', $data->nilai_durasi) }}" placeholder="0">
                                            @error('nilai_durasi')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" id="is_aktif" name="is_aktif"
                                            value="1" {{ old('is_aktif', $data->is_aktif) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_aktif">Aktifkan Paket</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-5">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0">Harga & Fitur</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label" for="harga_dasar">Harga Dasar (Rp) <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number"
                                                class="form-control @error('harga_dasar') is-invalid @enderror"
                                                id="harga_dasar" name="harga_dasar"
                                                value="{{ old('harga_dasar', (int) $data->harga_dasar) }}" placeholder="0">
                                        </div>
                                        @error('harga_dasar')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label" for="harga_overtime">Harga Overtime (Rp)</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number"
                                                class="form-control @error('harga_overtime') is-invalid @enderror"
                                                id="harga_overtime" name="harga_overtime"
                                                value="{{ old('harga_overtime', (int) $data->harga_overtime) }}"
                                                placeholder="0">
                                        </div>
                                        @error('harga_overtime')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label" for="satuan_overtime">Satuan Overtime</label>
                                        <select class="form-select @error('satuan_overtime') is-invalid @enderror"
                                            id="satuan_overtime" name="satuan_overtime">
                                            <option value="">N/A</option>
                                            <option value="jam"
                                                {{ old('satuan_overtime', $data->satuan_overtime) == 'jam' ? 'selected' : '' }}>
                                                Per Jam</option>
                                            <option value="foto"
                                                {{ old('satuan_overtime', $data->satuan_overtime) == 'foto' ? 'selected' : '' }}>
                                                Per Foto</option>
                                            <option value="menit"
                                                {{ old('satuan_overtime', $data->satuan_overtime) == 'menit' ? 'selected' : '' }}>
                                                Per Menit</option>
                                        </select>
                                        @error('satuan_overtime')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <hr>

                                    <label class="form-label">Fitur Paket</label>
                                    <div id="fitur-container">
                                        @php
                                            $fiturs = old('fitur', $data->fitur ?? []);
                                        @endphp

                                        @forelse($fiturs as $fitur)
                                            <div class="input-group mb-2 fitur-item">
                                                <input type="text" name="fitur[]" class="form-control"
                                                    value="{{ $fitur }}" placeholder="Contoh: CD/DVD Album">
                                                <button class="btn btn-outline-danger remove-fitur" type="button"><i
                                                        class="ri-close-line"></i></button>
                                            </div>
                                        @empty
                                            <div class="input-group mb-2 fitur-item">
                                                <input type="text" name="fitur[]" class="form-control"
                                                    placeholder="Contoh: 1 Fotografer">
                                                <button class="btn btn-outline-danger remove-fitur" type="button"><i
                                                        class="ri-close-line"></i></button>
                                            </div>
                                        @endforelse
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-primary mt-1" id="add-fitur">
                                        <i class="ri-add-line me-1"></i> Tambah Fitur
                                    </button>
                                </div>
                            </div>

                            <div class="card card-body">
                                <button type="submit" class="btn btn-primary w-100">Update Paket Layanan</button>
                                <a href="{{ route('paket-layanan.index') }}"
                                    class="btn btn-label-secondary w-100 mt-2">Batal</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('fitur-container');
            const addButton = document.getElementById('add-fitur');

            addButton.addEventListener('click', function() {
                const div = document.createElement('div');
                div.className = 'input-group mb-2 fitur-item';
                div.innerHTML = `
                <input type="text" name="fitur[]" class="form-control" placeholder="Fitur baru...">
                <button class="btn btn-outline-danger remove-fitur" type="button"><i class="ri-close-line"></i></button>
            `;
                container.appendChild(div);
            });

            container.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-fitur') || e.target.parentElement.classList
                    .contains('remove-fitur')) {
                    const item = e.target.closest('.fitur-item');
                    if (container.children.length > 1) {
                        item.remove();
                    } else {
                        item.querySelector('input').value = '';
                    }
                }
            });
        });
    </script>
@endsection
