<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;

    protected $table = 'tbl_pembayaran';

    protected $fillable = [
        'pesanan_id',
        'metode_pembayaran',
        'jumlah',
        'status',
        'waktu_bayar',
        'bukti_gambar',
        'catatan',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'waktu_bayar' => 'datetime',
    ];

    /**
     * Relasi ke Pesanan
     */
    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'pesanan_id');
    }
}
