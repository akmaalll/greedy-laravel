@extends('layouts/layoutMaster')

@section('title', 'Detail Penugasan')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">
            <span class="text-muted fw-light">Penugasan /</span> Detail Penugasan #{{ $data->pesanan->nomor_pesanan }}
        </h4>
        <a href="{{ route('penugasan-fotografer.index') }}" class="btn btn-outline-secondary">
            <i class="ri-arrow-left-line me-1"></i> Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header border-bottom">
                    <h5 class="mb-0">Informasi Acara</h5>
                </div>
                <div class="card-body pt-4">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-1">Lokasi:</h6>
                            <p class="h6"><i class="ri-map-pin-line me-1"></i> {{ $data->pesanan->lokasi_acara }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-1">Jadwal:</h6>
                            <p class="h6"><i class="ri-calendar-event-line me-1"></i> {{ $data->pesanan->tanggal_acara ? \Carbon\Carbon::parse($data->pesanan->tanggal_acara)->format('d M Y') : '-' }} ({{ $data->pesanan->waktu_mulai_acara ?? '-' }})</p>
                        </div>
                    </div>
                    
                    <h6 class="text-muted mb-2">Detail Paket:</h6>
                    <ul class="list-group list-group-flush border rounded mb-4">
                        @foreach($data->pesanan->items as $item)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <span class="fw-bold">{{ $item->paketLayanan->nama }}</span>
                                <br>
                                <small class="text-muted">{{ $item->paketLayanan->layanan->nama }}</small>
                            </div>
                            <span class="badge bg-label-primary">{{ $item->jumlah }}x</span>
                        </li>
                        @endforeach
                    </ul>

                    <h6>Catatan Penugasan:</h6>
                    <div class="p-3 bg-light rounded border-start border-primary border-3">
                        {{ $data->catatan ?: 'Tidak ada catatan khusus untuk penugasan ini.' }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header border-bottom">
                    <h5 class="mb-0">Status & Aksi</h5>
                </div>
                <div class="card-body pt-4">
                    @php
                        $statusInfo = [
                            'ditugaskan' => ['class' => 'warning', 'label' => 'DITUGASKAN'],
                            'diterima' => ['class' => 'info', 'label' => 'DITERIMA'],
                            'ditolak' => ['class' => 'danger', 'label' => 'DITOLAK'],
                            'selesai' => ['class' => 'success', 'label' => 'SELESAI']
                        ];
                        $info = $statusInfo[$data->status] ?? ['class' => 'secondary', 'label' => 'N/A'];
                    @endphp
                    
                    <div class="alert alert-{{ $info['class'] }} text-center mb-4">
                        <h4 class="alert-heading mb-1">{{ $info['label'] }}</h4>
                        <small>Status penugasan Anda saat ini</small>
                    </div>

                    @if(auth()->user()->role->slug == 'photographer' && $data->status == 'ditugaskan')
                    <div class="alert alert-info">
                        <small>Menunggu proses...</small>
                    </div>
                    @elseif(auth()->user()->role->slug == 'photographer' && $data->status == 'diterima')
                    <form action="{{ route('penugasan-fotografer.update', $data->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="status" value="selesai">
                        <div class="mb-3">
                            <label class="form-label">Catatan Hasil Kerja</label>
                            <textarea name="catatan" class="form-control" rows="3" placeholder="Masukkan catatan jika ada..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="ri-flag-line me-1"></i> Tandai Selesai
                        </button>
                    </form>
                    @endif

                    @if($data->waktu_diterima)
                    <hr>
                    <div class="small">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Waktu Diterima:</span>
                            <span>{{ $data->waktu_diterima ? \Carbon\Carbon::parse($data->waktu_diterima)->format('d/m/y H:i') : '-' }}</span>
                        </div>
                        @if($data->waktu_selesai)
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Waktu Selesai:</span>
                            <span>{{ $data->waktu_selesai ? \Carbon\Carbon::parse($data->waktu_selesai)->format('d/m/y H:i') : '-' }}</span>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
