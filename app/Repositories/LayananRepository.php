<?php

namespace App\Repositories;

use App\Models\Layanan;
use App\Interfaces\Repositories\LayananRepositoryInterface;

class LayananRepository extends BaseRepository implements LayananRepositoryInterface
{
    public function __construct(Layanan $model)
    {
        $this->model = $model;
    }
}
