<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    use HasFactory;

    protected $table = 'tbl_pesanan';

    protected $fillable = [
        'nomor_pesanan',
        'klien_id',
        'tanggal_acara',
        'waktu_mulai_acara',
        'waktu_selesai_acara',
        'lokasi_acara',
        'deskripsi_acara',
        'status',
        'subtotal',
        'biaya_overtime',
        'total_harga',
        'catatan',
    ];

    protected $casts = [
        'tanggal_acara' => 'date',
        'subtotal' => 'decimal:2',
        'biaya_overtime' => 'decimal:2',
        'total_harga' => 'decimal:2',
    ];

    /**
     * Relasi ke User (Klien)
     */
    public function klien()
    {
        return $this->belongsTo(User::class, 'klien_id');
    }

    /**
     * Relasi ke ItemPesanan
     */
    public function itemPesanan()
    {
        return $this->hasMany(ItemPesanan::class, 'pesanan_id');
    }

    /**
     * Alias untuk itemPesanan (agar konsisten dengan request dan view)
     */
    public function items()
    {
        return $this->hasMany(ItemPesanan::class, 'pesanan_id');
    }

    /**
     * Relasi ke PenugasanFotografer
     */
    public function penugasanFotografer()
    {
        return $this->hasMany(PenugasanFotografer::class, 'pesanan_id');
    }

    /**
     * Relasi ke Pembayaran
     */
    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class, 'pesanan_id');
    }

    /**
     * Relasi ke RatingFotografer
     */
    public function ratingFotografer()
    {
        return $this->hasMany(RatingFotografer::class, 'pesanan_id');
    }

    /**
     * Relasi ke RatingLayanan
     */
    public function ratingLayanan()
    {
        return $this->hasOne(RatingLayanan::class, 'pesanan_id');
    }
}
