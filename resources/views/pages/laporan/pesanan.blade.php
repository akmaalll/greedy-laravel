@extends('layouts/layoutMaster')

@section('title', 'Laporan Pesanan')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">
            <span class="text-muted fw-light">Laporan /</span> Pesanan
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

    <!-- Stats -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-label-primary">
                <div class="card-body text-center">
                    <h5 class="card-title mb-1">Total Pesanan</h5>
                    <h2 class="mb-0">{{ $statistics['total'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-label-success">
                <div class="card-body text-center">
                    <h5 class="card-title mb-1">Pesanan Selesai</h5>
                    <h2 class="mb-0">{{ $statistics['selesai'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-label-info">
                <div class="card-body text-center">
                    <h5 class="card-title mb-1">Total Nilai Pesanan</h5>
                    <h2 class="mb-0">Rp {{ number_format($statistics['total_nilai'], 0, ',', '.') }}</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-0">Daftar Pesanan Periode: {{ \Carbon\Carbon::parse($start)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($end)->format('d/m/Y') }}</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>No. Pesanan</th>
                        <th>Klien</th>
                        <th>Tgl Acara</th>
                        <th>Status</th>
                        <th class="text-end">Total Harga</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $item)
                    <tr>
                        <td><span class="fw-bold">{{ $item->nomor_pesanan }}</span></td>
                        <td>{{ $item->klien->name }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->tanggal_acara)->format('d/m/Y') }}</td>
                        <td>
                            <span class="badge bg-label-{{ $item->status == 'selesai' ? 'success' : ($item->status == 'dibatalkan' ? 'danger' : 'warning') }}">
                                {{ strtoupper($item->status) }}
                            </span>
                        </td>
                        <td class="text-end fw-bold">Rp {{ number_format($item->total_harga, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">Tidak ada data pesanan pada periode ini.</td>
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
