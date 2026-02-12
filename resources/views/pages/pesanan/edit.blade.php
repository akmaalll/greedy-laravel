@extends('layouts/layoutMaster')

@section('title', 'Edit Pesanan')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="mb-4">
        <h4 class="fw-bold mb-0">
            <span class="text-muted fw-light">Transaksi /</span> Edit Pesanan #{{ $data->nomor_pesanan }}
        </h4>
    </div>

    <form action="{{ route('pesanan.update', $data->id) }}" method="POST">
        @csrf
        @method('PUT')
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
                                <label class="form-label" for="tanggal_acara">Tanggal Acara <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('tanggal_acara') is-invalid @enderror" id="tanggal_acara" name="tanggal_acara" value="{{ old('tanggal_acara', $data->tanggal_acara ? $data->tanggal_acara->format('Y-m-d') : '') }}">
                                @error('tanggal_acara')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="waktu_mulai_acara">Waktu Mulai <span class="text-danger">*</span></label>
                                @php
                                    $timeValue = old('waktu_mulai_acara', $data->waktu_mulai_acara);
                                    if ($timeValue) {
                                        $timeValue = \Carbon\Carbon::parse($timeValue)->format('H:i');
                                    }
                                @endphp
                                <input type="time" class="form-control @error('waktu_mulai_acara') is-invalid @enderror" id="waktu_mulai_acara" name="waktu_mulai_acara" value="{{ $timeValue }}">
                                @error('waktu_mulai_acara')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="lokasi_acara">Lokasi Acara <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('lokasi_acara') is-invalid @enderror" id="lokasi_acara" name="lokasi_acara" value="{{ old('lokasi_acara', $data->lokasi_acara) }}" placeholder="Contoh: Gedung Serbaguna, Jakarta">
                            @error('lokasi_acara')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="deskripsi_acara">Deskripsi / Detail Acara</label>
                            <textarea class="form-control @error('deskripsi_acara') is-invalid @enderror" id="deskripsi_acara" name="deskripsi_acara" rows="4" placeholder="...">{{ old('deskripsi_acara', $data->deskripsi_acara) }}</textarea>
                            @error('deskripsi_acara')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        @if(auth()->user()->role->slug == 'admin')
                        <div class="mb-3">
                            <label class="form-label" for="status">Status Pesanan</label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                                <option value="menunggu" {{ old('status', $data->status) == 'menunggu' ? 'selected' : '' }}>Menunggu Konfirmasi</option>
                                <option value="dikonfirmasi" {{ old('status', $data->status) == 'dikonfirmasi' ? 'selected' : '' }}>Dikonfirmasi</option>
                                <option value="berlangsung" {{ old('status', $data->status) == 'berlangsung' ? 'selected' : '' }}>Berlangsung</option>
                                <option value="selesai" {{ old('status', $data->status) == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                <option value="dibatalkan" {{ old('status', $data->status) == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        @else
                        <input type="hidden" name="status" value="{{ $data->status }}">
                        @endif

                        <div class="mb-3">
                            <label class="form-label" for="catatan">Catatan Admin</label>
                            <textarea class="form-control @error('catatan') is-invalid @enderror" id="catatan" name="catatan" rows="2" placeholder="...">{{ old('catatan', $data->catatan) }}</textarea>
                            @error('catatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-5">
                <div class="card mb-4">
                    <div class="card-header border-bottom">
                        <h5 class="mb-0">Rincian Pembayaran (Estimasi)</h5>
                    </div>
                    <div class="card-body pt-4">
                        <div class="mb-4">
                            <h6 class="mb-2">Paket yang Dipilih:</h6>
                            <div class="table-responsive border rounded">
                                <table class="table table-sm mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Paket</th>
                                            <th class="text-center">Jml</th>
                                            <th class="text-end">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($data->itemPesanan as $item)
                                        <tr>
                                            <td><small>{{ $item->paketLayanan->nama }}</small></td>
                                            <td class="text-center">{{ $item->jumlah }}</td>
                                            <td class="text-end">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="alert alert-info mt-2 mb-0">
                                <small><i class="ri-information-line me-1"></i> Item paket tidak dapat diubah di halaman ini untuk menjaga konsistensi harga. Silakan buat pesanan baru jika ingin merombak paket.</small>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal Paket</span>
                            <span class="fw-bold">Rp {{ number_format($data->subtotal, 0, ',', '.') }}</span>
                        </div>
                        
                        @if(auth()->user()->role->slug == 'admin')
                        <div class="mb-3">
                            <label class="form-label" for="biaya_overtime">Biaya Overtime (Manual)</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control" name="biaya_overtime" value="{{ old('biaya_overtime', (int)$data->biaya_overtime) }}">
                            </div>
                        </div>
                        @else
                        <div class="d-flex justify-content-between mb-2 text-muted">
                            <span>Biaya Overtime</span>
                            <span>Rp {{ number_format($data->biaya_overtime, 0, ',', '.') }}</span>
                        </div>
                        @endif

                        <div class="d-flex justify-content-between mb-4 border-top pt-2">
                            <span class="h5">Total Harga</span>
                            <span class="h5 text-primary">Rp {{ number_format($data->total_harga, 0, ',', '.') }}</span>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Simpan Perubahan</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
