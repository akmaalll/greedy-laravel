<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalPesanan extends Model
{
    use HasFactory;

    protected $table = 'tbl_jadwal_pesanan';

    protected $fillable = [
        'pesanan_id',
        'jadwal_mulai',
        'jadwal_selesai',
        'aktual_mulai',
        'aktual_selesai',
        'catatan',
    ];

    protected $casts = [
        'jadwal_mulai' => 'datetime',
        'jadwal_selesai' => 'datetime',
        'aktual_mulai' => 'datetime',
        'aktual_selesai' => 'datetime',
    ];

    /**
     * Relasi ke Pesanan
     */
    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'pesanan_id');
    }
}
