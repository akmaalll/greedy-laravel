@php
$configData = Helper::appClasses();
@endphp
@extends('layouts/layoutMaster')

@section('title', 'Dashboard - Klien')

@section('content')
<div class="row g-6">
  <!-- welcome Card -->
  <div class="col-md-12 col-xxl-8">
    <div class="card">
      <div class="d-flex align-items-end row">
        <div class="col-md-6 order-2 order-md-1">
          <div class="card-body">
            <h4 class="card-title mb-4">Selamat Datang, <span class="fw-bold">{{ auth()->user()->name }}!</span> ✨</h4>
            <p class="mb-0">Terima kasih telah mempercayakan kenangan Anda kepada kami.</p>
            <p>Total investasi kenangan Anda: <span class="fw-bold text-primary">Rp {{ number_format($total_spending, 0, ',', '.') }}</span></p>
            {{-- <a href="{{ route('pesanan.create') }}" class="btn btn-primary">Booking Layanan Baru</a> --}}
          </div>
        </div>
        <div class="col-md-6 text-center text-md-end order-1 order-md-2">
          <div class="card-body pb-0 px-0 pt-2">
            <img src="{{asset('assets/img/illustrations/illustration-john-'.$configData['style'].'.png')}}" height="186" class="scaleX-n1-rtl" alt="View Profile" data-app-light-img="illustrations/illustration-john-light.png" data-app-dark-img="illustrations/illustration-john-dark.png">
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Client Stats -->
  <div class="col-xxl-4">
    <div class="row g-4">
      <div class="col-12">
        <div class="card">
          <div class="card-body d-flex align-items-center justify-content-between">
            <div>
              <h5 class="mb-1">Total Pesanan</h5>
              <p class="mb-0">{{ $my_orders_count }} Transaksi</p>
            </div>
            <div class="avatar avatar-md border-primary border-2">
              <span class="avatar-initial rounded-circle bg-label-primary"><i class="ri-heart-line ri-24px text-primary"></i></span>
            </div>
          </div>
        </div>
      </div>
      <div class="col-12">
        <div class="card bg-primary text-white">
          <div class="card-body d-flex align-items-center justify-content-between">
            <div>
              <h5 class="mb-1 text-white">Status Aktif</h5>
              <p class="mb-0">{{ $active_orders->count() }} Pesanan Berjalan</p>
            </div>
            <div class="avatar avatar-md">
              <span class="avatar-initial rounded-circle bg-white text-primary"><i class="ri-refresh-line ri-24px"></i></span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Recent Orders Table -->
  <div class="col-12">
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="card-title m-0">Riwayat Pesanan Terbaru</h5>
            <a href="{{ route('pesanan.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Lokasi</th>
                        <th>Tanggal</th>
                        <th>Total Harga</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recent_history as $order)
                    <tr>
                        <td class="fw-bold text-primary">#{{ $order->nomor_pesanan }}</td>
                        <td>{{ $order->lokasi_acara }}</td>
                        <td>{{ \Carbon\Carbon::parse($order->tanggal_acara)->format('d M Y') }}</td>
                        <td class="fw-bold">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</td>
                        <td>
                            @php
                                $statusColors = [
                                    'menunggu' => 'warning',
                                    'dikonfirmasi' => 'primary',
                                    'berlangsung' => 'info',
                                    'selesai' => 'success',
                                    'dibatalkan' => 'danger',
                                ];
                                $color = $statusColors[$order->status] ?? 'secondary';
                            @endphp
                            <span class="badge bg-label-{{ $color }} text-uppercase">
                                {{ $order->status }}
                            </span>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('pesanan.show', $order->id) }}" class="btn btn-sm btn-icon btn-text-secondary rounded-pill">
                                <i class="ri-eye-line"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">Belum ada riwayat pesanan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
  </div>
</div>
@endsection
