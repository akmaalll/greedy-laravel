<?php

namespace App\Repositories;

use App\Models\JadwalPesanan;
use App\Interfaces\Repositories\JadwalPesananRepositoryInterface;

class JadwalPesananRepository extends BaseRepository implements JadwalPesananRepositoryInterface
{
    public function __construct(JadwalPesanan $model)
    {
        $this->model = $model;
    }
}
