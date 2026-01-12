<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RatingLayanan extends Model
{
    use HasFactory;

    protected $table = 'tbl_rating_layanan';

    protected $fillable = [
        'pesanan_id',
        'klien_id',
        'rating',
        'ulasan',
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    /**
     * Relasi ke Pesanan
     */
    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'pesanan_id');
    }

    /**
     * Relasi ke User (Klien)
     */
    public function klien()
    {
        return $this->belongsTo(User::class, 'klien_id');
    }
}
