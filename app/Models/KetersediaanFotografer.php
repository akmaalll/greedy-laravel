<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KetersediaanFotografer extends Model
{
    use HasFactory;

    protected $table = 'tbl_ketersediaan_fotografer';

    protected $fillable = [
        'fotografer_id',
        'tanggal',
        'waktu_mulai',
        'waktu_selesai',
        'status',
        'catatan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    /**
     * Relasi ke User (Fotografer)
     */
    public function fotografer()
    {
        return $this->belongsTo(User::class, 'fotografer_id');
    }
}
