<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use HasFactory;

    protected $table = 'tbl_kategori';

    protected $fillable = [
        'nama',
        'slug',
        'deskripsi',
        'is_aktif',
    ];

    protected $casts = [
        'is_aktif' => 'boolean',
    ];

    /**
     * Relasi ke Layanan
     * Satu kategori memiliki banyak layanan
     */
    public function layanan()
    {
        return $this->hasMany(Layanan::class, 'kategori_id');
    }
}
