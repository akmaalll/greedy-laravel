<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemPesanan extends Model
{
    use HasFactory;

    protected $table = 'tbl_item_pesanan';

    protected $fillable = [
        'pesanan_id',
        'paket_layanan_id',
        'jumlah',
        'harga_satuan',
        'jam_overtime',
        'biaya_overtime',
        'subtotal',
    ];

    protected $casts = [
        'harga_satuan' => 'decimal:2',
        'jam_overtime' => 'decimal:2',
        'biaya_overtime' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    /**
     * Relasi ke Pesanan
     */
    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'pesanan_id');
    }

    /**
     * Relasi ke PaketLayanan
     */
    public function paketLayanan()
    {
        return $this->belongsTo(PaketLayanan::class, 'paket_layanan_id');
    }
}
