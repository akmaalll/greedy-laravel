@php
$configData = Helper::appClasses();
@endphp
@extends('layouts/layoutMaster')

@section('title', 'Dashboard - Fotografer')

@section('content')
<div class="row g-6">
  <!-- welcome Card -->
  <div class="col-md-12 col-xxl-8">
    <div class="card">
      <div class="d-flex align-items-end row">
        <div class="col-md-6 order-2 order-md-1">
          <div class="card-body">
            <h4 class="card-title mb-4">Halo, <span class="fw-bold">{{ auth()->user()->name }}!</span> 📸</h4>
            <p class="mb-0">Ada <span class="fw-bold text-primary">{{ $tasks_today }} tugas</span> untuk Anda hari ini.</p>
            <p>Berikan performa terbaikmu untuk hasil yang maksimal!</p>
            <a href="{{ route('penugasan-fotografer.index') }}" class="btn btn-primary">Lihat Daftar Tugas</a>
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

  <!-- Performance Stats -->
  <div class="col-xxl-4">
    <div class="row g-4">
      <div class="col-6">
        <div class="card">
          <div class="card-body text-center">
            <div class="avatar avatar-md mx-auto mb-3">
              <span class="avatar-initial rounded-circle bg-label-success"><i class="ri-check-double-line ri-24px"></i></span>
            </div>
            <h5 class="mb-1">{{ $completed_tasks }}</h5>
            <p class="mb-0">Tugas Selesai</p>
          </div>
        </div>
      </div>
      <div class="col-6">
        <div class="card">
          <div class="card-body text-center">
            <div class="avatar avatar-md mx-auto mb-3">
              <span class="avatar-initial rounded-circle bg-label-warning"><i class="ri-star-smile-line ri-24px"></i></span>
            </div>
            <h5 class="mb-1">{{ number_format($avg_rating, 1) }}</h5>
            <p class="mb-0">Rating Rata-rata</p>
          </div>
        </div>
      </div>
      <div class="col-12">
        <div class="card">
          <div class="card-body d-flex align-items-center justify-content-between">
            <div>
              <h5 class="mb-1">Tugas Hari Ini</h5>
              <p class="mb-0">{{ $tasks_today }} item</p>
            </div>
            <div class="avatar avatar-md">
              <span class="avatar-initial rounded-circle bg-label-primary"><i class="ri-calendar-event-line ri-24px"></i></span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Recent Jobs Table -->
  <div class="col-12">
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="card-title m-0 me-2">Daftar Penugasan Terbaru</h5>
            <a href="{{ route('penugasan-fotografer.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Acara</th>
                        <th>Klien</th>
                        <th>Tanggal</th>
                        <th>Waktu</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recent_jobs as $job)
                    <tr>
                        <td>{{ $job->pesanan->lokasi_acara }}</td>
                        <td>{{ $job->pesanan->klien->name }}</td>
                        <td>{{ \Carbon\Carbon::parse($job->pesanan->tanggal_acara)->format('d M Y') }}</td>
                        <td>{{ $job->pesanan->waktu_mulai_acara }}</td>
                        <td>
                            @php
                                $statusColors = [
                                    'ditugaskan' => 'warning',
                                    'diterima' => 'primary',
                                    'selesai' => 'success',
                                    'ditolak' => 'danger',
                                ];
                            @endphp
                            <span class="badge bg-label-{{ $statusColors[$job->status] ?? 'secondary' }}">
                                {{ ucfirst($job->status) }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('penugasan-fotografer.show', $job->id) }}" class="btn btn-sm btn-icon btn-text-secondary rounded-pill">
                                <i class="ri-eye-line"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">Belum ada penugasan yang diberikan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
  </div>
</div>
@endsection
