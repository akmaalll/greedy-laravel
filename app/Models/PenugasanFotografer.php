<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenugasanFotografer extends Model
{
    use HasFactory;

    protected $table = 'tbl_penugasan_fotografer';

    protected $fillable = [
        'pesanan_id',
        'fotografer_id',
        'status',
        'catatan',
        'waktu_ditugaskan',
        'waktu_diterima',
        'waktu_selesai',
    ];

    protected $casts = [
        'waktu_ditugaskan' => 'datetime',
        'waktu_diterima' => 'datetime',
        'waktu_selesai' => 'datetime',
    ];

    /**
     * Relasi ke Pesanan
     */
    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'pesanan_id');
    }

    /**
     * Relasi ke User (Fotografer)
     */
    public function fotografer()
    {
        return $this->belongsTo(User::class, 'fotografer_id');
    }
}
