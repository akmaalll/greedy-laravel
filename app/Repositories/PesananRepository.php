<?php

namespace App\Repositories;

use App\Models\Pesanan;
use App\Interfaces\Repositories\PesananRepositoryInterface;

class PesananRepository extends BaseRepository implements PesananRepositoryInterface
{
    public function __construct(Pesanan $model)
    {
        $this->model = $model;
    }
}
