<?php

namespace App\Repositories;

use App\Models\Kategori;
use App\Interfaces\Repositories\KategoriRepositoryInterface;

class KategoriRepository extends BaseRepository implements KategoriRepositoryInterface
{
    public function __construct(Kategori $model)
    {
        $this->model = $model;
    }
}
