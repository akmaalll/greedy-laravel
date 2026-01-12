@php
$configData = Helper::appClasses();
@endphp
@extends('layouts/layoutMaster')

@section('title', 'Dashboard - Admin')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/apex-charts/apex-charts.scss'
])
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/apex-charts/apexcharts.js'
])
@endsection

@section('content')
<div class="row g-6">
  <!-- welcome Card -->
  <div class="col-md-12 col-xxl-8">
    <div class="card">
      <div class="d-flex align-items-end row">
        <div class="col-md-6 order-2 order-md-1">
          <div class="card-body">
            <h4 class="card-title mb-4">Selamat Datang, <span class="fw-bold">{{ auth()->user()->name }}!</span> 👋</h4>
            <p class="mb-0">Bisnis Anda sedang berjalan dengan baik.</p>
            <p>Total pendapatan terkumpul: <span class="fw-bold text-success">Rp {{ number_format($total_revenue, 0, ',', '.') }}</span></p>
            <a href="{{ route('pesanan.index') }}" class="btn btn-primary">Kelola Pesanan</a>
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
  
  <!-- Stats -->
  <div class="col-xxl-4">
    <div class="row g-4">
      <div class="col-6">
        <div class="card">
          <div class="card-body text-center">
            <div class="avatar avatar-md mx-auto mb-3">
              <span class="avatar-initial rounded-circle bg-label-primary"><i class="ri-shopping-cart-2-line ri-24px"></i></span>
            </div>
            <h5 class="mb-1">{{ $total_orders }}</h5>
            <p class="mb-0">Total Pesanan</p>
          </div>
        </div>
      </div>
      <div class="col-6">
        <div class="card">
          <div class="card-body text-center">
            <div class="avatar avatar-md mx-auto mb-3">
              <span class="avatar-initial rounded-circle bg-label-warning"><i class="ri-time-line ri-24px"></i></span>
            </div>
            <h5 class="mb-1">{{ $pending_orders }}</h5>
            <p class="mb-0">Menunggu</p>
          </div>
        </div>
      </div>
      <div class="col-6">
        <div class="card">
          <div class="card-body text-center">
            <div class="avatar avatar-md mx-auto mb-3">
              <span class="avatar-initial rounded-circle bg-label-success"><i class="ri-user-star-line ri-24px"></i></span>
            </div>
            <h5 class="mb-1">{{ $active_photographers }}</h5>
            <p class="mb-0">Fotografer</p>
          </div>
        </div>
      </div>
       <div class="col-6">
        <div class="card">
          <div class="card-body text-center">
            <div class="avatar avatar-md mx-auto mb-3">
              <span class="avatar-initial rounded-circle bg-label-info"><i class="ri-money-dollar-circle-line ri-24px"></i></span>
            </div>
            <h5 class="mb-1">Stat</h5>
            <p class="mb-0">Revenue</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Recent Orders Table -->
  <div class="col-12 col-xl-8">
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="card-title m-0 me-2">Pesanan Terbaru</h5>
            <a href="{{ route('pesanan.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Klien</th>
                        <th>Tanggal Acara</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recent_orders as $order)
                    <tr>
                        <td>#{{ $order->nomor_pesanan }}</td>
                        <td>{{ $order->klien->name }}</td>
                        <td>{{ \Carbon\Carbon::parse($order->tanggal_acara)->format('d/m/Y') }}</td>
                        <td>Rp {{ number_format($order->total_harga, 0, ',', '.') }}</td>
                        <td>
                            @php
                                $statusColors = [
                                    'menunggu' => 'warning',
                                    'dikonfirmasi' => 'primary',
                                    'berlangsung' => 'info',
                                    'selesai' => 'success',
                                    'dibatalkan' => 'danger',
                                ];
                            @endphp
                            <span class="badge bg-label-{{ $statusColors[$order->status] ?? 'secondary' }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
  </div>

  <!-- Activity Timeline or Chart placeholder -->
  <div class="col-12 col-xl-4">
    <div class="card h-100">
        <div class="card-header">
            <h5 class="mb-0">Statistik Bulanan</h5>
        </div>
        <div class="card-body">
            <p class="text-muted small">Ringkasan pendapatan bulan demi bulan di tahun {{ date('Y') }}.</p>
            <div id="monthlyEarningsChart"></div>
        </div>
    </div>
  </div>
</div>
@endsection

@section('page-script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const earningsData = @json($monthly_earnings);
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        
        let categories = [];
        let data = new Array(12).fill(0);

        earningsData.forEach(item => {
            data[item.month - 1] = parseFloat(item.total);
        });

        const options = {
            chart: {
                type: 'bar',
                height: 200,
                toolbar: { show: false }
            },
            series: [{
                name: 'Pendapatan',
                data: data
            }],
            xaxis: {
                categories: months,
                position: 'bottom',
            },
            colors: ['#696cff'],
            plotOptions: {
                bar: {
                    borderRadius: 4,
                    columnWidth: '50%',
                }
            },
            dataLabels: { enabled: false },
        };

        const chart = new ApexCharts(document.querySelector("#monthlyEarningsChart"), options);
        chart.render();
    });
</script>
@endsection
