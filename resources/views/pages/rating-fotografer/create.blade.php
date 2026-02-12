@extends('layouts/layoutMaster')

@section('title', 'Beri Rating Fotografer')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="mb-4">
        <h4 class="fw-bold mb-0">
            <span class="text-muted fw-light">Rating /</span> Rating Fotografer
        </h4>
    </div>

    <div class="row">
        <div class="col-md-6 mx-auto">
            <div class="card">
                <div class="card-header border-bottom">
                    <h5 class="mb-0">Beri Ulasan Fotografer</h5>
                </div>
                <div class="card-body pt-4 text-center">
                    <div class="mb-4">
                        <div class="avatar avatar-xl mx-auto mb-2">
                            <span class="avatar-initial rounded-circle bg-label-info"><i class="ri-user-star-line ri-40px"></i></span>
                        </div>
                        <h5 class="mb-1">{{ $pesanan->penugasanFotografer->first()->fotografer->name ?? 'Fotografer' }}</h5>
                        <p class="text-muted small">Untuk Pesanan #{{ $pesanan->nomor_pesanan }}</p>
                    </div>

                    <form action="{{ route('rating-fotografer.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="pesanan_id" value="{{ $pesanan->id }}">
                        <input type="hidden" name="fotografer_id" value="{{ $pesanan->penugasanFotografer->first()->fotografer_id ?? '' }}">
                        
                        <div class="mb-4">
                            <label class="form-label d-block mb-3">Seberapa puas Anda dengan kinerja fotografer?</label>
                            <div class="star-rating h2 text-warning">
                                <i class="ri-star-line star-item" data-value="1" style="cursor: pointer"></i>
                                <i class="ri-star-line star-item" data-value="2" style="cursor: pointer"></i>
                                <i class="ri-star-line star-item" data-value="3" style="cursor: pointer"></i>
                                <i class="ri-star-line star-item" data-value="4" style="cursor: pointer"></i>
                                <i class="ri-star-line star-item" data-value="5" style="cursor: pointer"></i>
                            </div>
                            <input type="hidden" name="rating" id="rating-value" value="5">
                            @error('rating')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4 text-start">
                            <label class="form-label" for="ulasan">Ulasan Anda (Opsional)</label>
                            <textarea class="form-control @error('ulasan') is-invalid @enderror" id="ulasan" name="ulasan" rows="4" placeholder="Bagikan pengalaman Anda bekerja dengan fotografer ini..."></textarea>
                            @error('ulasan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Kirim Ulasan</button>
                            <a href="{{ route('pesanan.show', $pesanan->id) }}" class="btn btn-label-secondary mt-2">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const stars = document.querySelectorAll('.star-item');
        const ratingInput = document.getElementById('rating-value');

        function updateStars(value) {
            stars.forEach(star => {
                const starVal = star.dataset.value;
                if (starVal <= value) {
                    star.classList.remove('ri-star-line');
                    star.classList.add('ri-star-fill');
                } else {
                    star.classList.remove('ri-star-fill');
                    star.classList.add('ri-star-line');
                }
            });
        }

        // Initialize with default value 5
        updateStars(5);

        stars.forEach(star => {
            star.addEventListener('click', function() {
                const value = this.dataset.value;
                ratingInput.value = value;
                updateStars(value);
            });

            star.addEventListener('mouseover', function() {
                const value = this.dataset.value;
                updateStars(value);
            });

            star.addEventListener('mouseout', function() {
                updateStars(ratingInput.value);
            });
        });
    });
</script>
@endsection
