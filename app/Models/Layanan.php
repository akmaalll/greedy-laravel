<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Layanan extends Model
{
    use HasFactory;

    protected $table = 'tbl_layanan';

    protected $fillable = [
        'kategori_id',
        'nama',
        'tipe',
        'deskripsi',
        'is_aktif',
    ];

    protected $casts = [
        'is_aktif' => 'boolean',
    ];

    /**
     * Relasi ke Kategori
     */
    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    /**
     * Relasi ke PaketLayanan
     */
    public function paketLayanan()
    {
        return $this->hasMany(PaketLayanan::class, 'layanan_id');
    }
}
