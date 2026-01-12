<?php

namespace App\Repositories;

use App\Models\Pembayaran;
use App\Interfaces\Repositories\PembayaranRepositoryInterface;

class PembayaranRepository extends BaseRepository implements PembayaranRepositoryInterface
{
    public function __construct(Pembayaran $model)
    {
        $this->model = $model;
    }
}
