@extends('layouts/layoutMaster')

@section('title', 'Detail Pesanan')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">
            <span class="text-muted fw-light">Transaksi /</span> Detail Pesanan #{{ $data->nomor_pesanan }}
        </h4>
        <div>
            <a href="{{ route('pesanan.index') }}" class="btn btn-outline-secondary me-2">
                <i class="ri-arrow-left-line me-1"></i> Kembali
            </a>
            @if(auth()->user()->role->slug == 'admin' || (auth()->user()->role->slug == 'client' && $data->status == 'menunggu'))
                @if(auth()->user()->role->slug == 'admin' && $data->status == 'menunggu')
                <form action="{{ route('pesanan.update', $data->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="dikonfirmasi">
                    <input type="hidden" name="tanggal_acara" value="{{ $data->tanggal_acara }}">
                    <input type="hidden" name="waktu_mulai_acara" value="{{ $data->waktu_mulai_acara }}">
                    <input type="hidden" name="lokasi_acara" value="{{ $data->lokasi_acara }}">
                    <button type="submit" class="btn btn-success me-2">
                        <i class="ri-check-line me-1"></i> Konfirmasi Pesanan
                    </button>
                </form>
                @endif

                @if(auth()->user()->role->slug == 'admin' && $data->status == 'berlangsung')
                <form action="{{ route('pesanan.update', $data->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="selesai">
                    <input type="hidden" name="tanggal_acara" value="{{ $data->tanggal_acara }}">
                    <input type="hidden" name="waktu_mulai_acara" value="{{ $data->waktu_mulai_acara }}">
                    <input type="hidden" name="lokasi_acara" value="{{ $data->lokasi_acara }}">
                    <button type="submit" class="btn btn-success me-2">
                        <i class="ri-medal-line me-1"></i> Selesaikan Pesanan
                    </button>
                </form>
                @endif

                <a href="{{ route('pesanan.edit', $data->id) }}" class="btn btn-primary">
                    <i class="ri-pencil-line me-1"></i> Edit Pesanan
                </a>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header border-bottom">
                    <h5 class="mb-0">Rincian Acara & Layanan</h5>
                </div>
                <div class="card-body pt-4">
                    <div class="row mb-4">
                        <div class="col-sm-6">
                            <h6 class="text-muted mb-1">Lokasi Acara:</h6>
                            <p class="h6"><i class="ri-map-pin-2-line me-2 text-primary"></i>{{ $data->lokasi_acara }}</p>
                        </div>
                        <div class="col-sm-6">
                            <h6 class="text-muted mb-1">Tanggal & Waktu:</h6>
                            <p class="h6">
                                <i class="ri-calendar-line me-2 text-primary"></i>{{ \Carbon\Carbon::parse($data->tanggal_acara)->format('d F Y') }}
                                <br>
                                <i class="ri-time-line me-2 text-primary"></i>Pukul {{ $data->waktu_mulai_acara }} WIB
                            </p>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="text-muted mb-2">Deskripsi Acara:</h6>
                        <div class="p-3 bg-light rounded italic">
                            {{ $data->deskripsi_acara ?: 'Tidak ada deskripsi tambahan.' }}
                        </div>
                    </div>

                    <h6 class="mb-3">Daftar Paket:</h6>
                    <div class="table-responsive border rounded">
                        <table class="table table-bordered mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Paket Layanan</th>
                                    <th class="text-center" style="width: 100px">Jml</th>
                                    <th class="text-end">Harga</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data->itemPesanan as $item)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $item->paketLayanan->nama }}</div>
                                        <small class="text-muted">{{ $item->paketLayanan->layanan->nama }}</small>
                                    </td>
                                    <td class="text-center">{{ $item->jumlah }}</td>
                                    <td class="text-end">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                                    <td class="text-end fw-bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="border-top-2">
                                <tr>
                                    <td colspan="3" class="text-end fw-bold">Subtotal</td>
                                    <td class="text-end fw-bold">Rp {{ number_format($data->subtotal, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end">Biaya Overtime</td>
                                    <td class="text-end">Rp {{ number_format($data->biaya_overtime, 0, ',', '.') }}</td>
                                </tr>
                                <tr class="bg-label-primary">
                                    <td colspan="3" class="text-end h5 mb-0">Total Keseluruhan</td>
                                    <td class="text-end h5 mb-0">Rp {{ number_format($data->total_harga, 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header border-bottom">
                    <h5 class="mb-0">Status Pesanan</h5>
                </div>
                <div class="card-body pt-4">
                    <div class="d-flex align-items-center mb-4">
                        @php
                            $statusInfo = [
                                'menunggu' => ['class' => 'bg-label-warning', 'icon' => 'ri-time-line'],
                                'dikonfirmasi' => ['class' => 'bg-label-primary', 'icon' => 'ri-check-double-line'],
                                'berlangsung' => ['class' => 'bg-label-info', 'icon' => 'ri-loop-right-line'],
                                'selesai' => ['class' => 'bg-label-success', 'icon' => 'ri-medal-line'],
                                'dibatalkan' => ['class' => 'bg-label-danger', 'icon' => 'ri-close-circle-line']
                            ];
                            $info = $statusInfo[$data->status] ?? ['class' => 'bg-label-secondary', 'icon' => 'ri-question-line'];
                        @endphp
                        <div class="avatar avatar-md me-3">
                            <span class="avatar-initial rounded {{ $info['class'] }}"><i class="{{ $info['icon'] }} ri-24px"></i></span>
                        </div>
                        <div>
                            <h6 class="mb-0">Status: {{ strtoupper($data->status) }}</h6>
                            <small class="text-muted">Terakhir diupdate: {{ $data->updated_at->diffForHumans() }}</small>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="small fw-bold text-muted mb-1 d-block">PEMBERI PESAN (KLIEN)</label>
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-sm me-2">
                                <span class="avatar-initial rounded-circle bg-label-dark">{{ substr($data->klien->name, 0, 1) }}</span>
                            </div>
                            <div>
                                <h6 class="mb-0 font-small">{{ $data->klien->name }}</h6>
                                <small class="text-muted">{{ $data->klien->email }}</small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="small fw-bold text-muted mb-1 d-block">PROGRES PEMBAYARAN</label>
                        @php
                            $terbayar = $data->pembayaran->where('status', 'lunas')->sum('jumlah');
                            $sisa = $data->total_harga - $terbayar;
                            $persen = $data->total_harga > 0 ? ($terbayar / $data->total_harga) * 100 : 0;
                        @endphp
                        <div class="progress mb-1" style="height: 10px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $persen }}%" aria-valuenow="{{ $persen }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="d-flex justify-content-between">
                            <small class="fw-semibold">Terbayar: Rp {{ number_format($terbayar, 0, ',', '.') }}</small>
                            <small class="text-danger">Sisa: Rp {{ number_format($sisa, 0, ',', '.') }}</small>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="small fw-bold text-muted mb-1 d-block">CATATAN INTERNAL</label>
                        <p class="small mb-0">{{ $data->catatan ?: '-' }}</p>
                    </div>

                    @if(auth()->user()->role->slug == 'client' && in_array($data->status, ['menunggu', 'dikonfirmasi', 'berlangsung']))
                    <div class="mt-4 pt-2 border-top">
                        <div class="d-grid gap-2">
                            @if($sisa > 0 && $data->status == 'dikonfirmasi')
                                @if($data->penugasanFotografer->isNotEmpty())
                                <a href="{{ route('pembayaran.create', ['pesanan_id' => $data->id]) }}" class="btn btn-success btn-lg">
                                    <i class="ri-bank-card-line me-1"></i> Bayar Sekarang
                                </a>
                                @else
                                <div class="alert alert-warning mb-0">
                                    <i class="ri-information-line me-1"></i> 
                                    Pembayaran dapat dilakukan setelah fotografer ditugaskan. Mohon tunggu sebentar.
                                </div>
                                @endif
                            @endif
                        </div>
                    </div>
                    @endif

                    @if(auth()->user()->role->slug == 'client' && $data->status == 'selesai')
                    <div class="mt-4 pt-2 border-top">
                        <div class="d-grid gap-2">
                            @if(!$data->ratingLayanan)
                            <a href="{{ route('rating-layanan.create', ['pesanan_id' => $data->id]) }}" class="btn btn-warning">
                                <i class="ri-star-smile-line me-1"></i> Beri Rating Layanan
                            </a>
                            @endif
                            
                            @if($data->penugasanFotografer->isNotEmpty())
                            <a href="{{ route('rating-fotografer.create', ['pesanan_id' => $data->id]) }}" class="btn btn-outline-warning">
                                <i class="ri-user-star-line me-1"></i> Beri Rating Fotografer
                            </a>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header border-bottom">
                    <h5 class="mb-0">Penugasan & Jadwal</h5>
                </div>
                <div class="card-body pt-4">
                    @forelse($data->penugasanFotografer as $tugas)
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar avatar-sm me-2">
                            <span class="avatar-initial rounded bg-label-info"><i class="ri-user-star-line"></i></span>
                        </div>
                        <div>
                            <h6 class="mb-0 small">{{ $tugas->fotografer->name }}</h6>
                            <span class="badge badge-sm bg-label-{{ $tugas->status == 'diterima' ? 'success' : 'warning' }}">{{ strtoupper($tugas->status) }}</span>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-2 text-muted">
                        <i class="ri-user-forbid-line ri-2x mb-1"></i>
                        <p class="small mb-0">Belum ada fotografer ditugaskan.</p>
                        @if(auth()->user()->role->slug == 'admin')
                        <button class="btn btn-sm btn-label-primary mt-2">Tugaskan Sekarang</button>
                        @endif
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
