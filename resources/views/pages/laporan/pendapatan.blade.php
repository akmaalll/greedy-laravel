@extends('layouts/layoutMaster')

@section('title', 'Laporan Pendapatan')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">
            <span class="text-muted fw-light">Laporan /</span> Pendapatan
        </h4>
        <button class="btn btn-outline-primary" onclick="window.print()">
            <i class="ri-printer-line me-1"></i> Cetak Laporan
        </button>
    </div>

    <!-- Filter -->
    <div class="card mb-4 no-print">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Tanggal Mulai</label>
                    <input type="date" name="start_date" class="form-control" value="{{ $start }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tanggal Selesai</label>
                    <input type="date" name="end_date" class="form-control" value="{{ $end }}">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="ri-filter-3-line me-1"></i> Filter Data
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h5 class="mb-1 text-muted">Total Pendapatan (Lunas):</h5>
                    <h1 class="mb-0 text-primary">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</h1>
                </div>
                <div class="col-sm-6 text-sm-end mt-3 mt-sm-0 text-muted">
                    <p class="mb-0">Periode:</p>
                    <p class="h5 mb-0 text-dark">{{ \Carbon\Carbon::parse($start)->format('d F Y') }} - {{ \Carbon\Carbon::parse($end)->format('d F Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-0">Rincian Transaksi Masuk</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Tgl Bayar</th>
                        <th>No. Pesanan</th>
                        <th>Klien</th>
                        <th>Metode</th>
                        <th class="text-end">Jumlah Bayar</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $item)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($item->tanggal_bayar)->format('d/m/Y') }}</td>
                        <td><span class="fw-bold">{{ $item->pesanan->nomor_pesanan }}</span></td>
                        <td>{{ $item->pesanan->klien->name }}</td>
                        <td>{{ strtoupper($item->metode_pembayaran) }}</td>
                        <td class="text-end fw-bold">Rp {{ number_format($item->jumlah, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">Belum ada pendapatan terekam pada periode ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
@media print {
    .no-print { display: none !important; }
    .container-p-y { padding-top: 0 !important; }
    .card { border: none !important; box-shadow: none !important; }
}
</style>
@endsection
