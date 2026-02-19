<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaketLayanan extends Model
{
    use HasFactory;

    protected $table = 'tbl_paket_layanan';

    protected $fillable = [
        'layanan_id',
        'nama',
        'deskripsi',
        'tipe_durasi',
        'nilai_durasi',
        'harga_dasar',
        'harga_overtime',
        'satuan_overtime',
        'fitur',
        'gambar',
        'video',
        'is_aktif',
    ];

    protected $casts = [
        'harga_dasar' => 'decimal:2',
        'harga_overtime' => 'decimal:2',
        'fitur' => 'array',
        'is_aktif' => 'boolean',
    ];

    /**
     * Relasi ke Layanan
     */
    public function layanan()
    {
        return $this->belongsTo(Layanan::class, 'layanan_id');
    }

    /**
     * Relasi ke ItemPesanan
     */
    public function itemPesanan()
    {
        return $this->hasMany(ItemPesanan::class, 'paket_layanan_id');
    }
}
