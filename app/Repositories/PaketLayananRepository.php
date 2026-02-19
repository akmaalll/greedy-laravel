<?php

namespace App\Repositories;

use App\Interfaces\Repositories\PaketLayananRepositoryInterface;
use App\Models\PaketLayanan;

class PaketLayananRepository extends BaseRepository implements PaketLayananRepositoryInterface
{
    public function __construct(PaketLayanan $model)
    {
        $this->model = $model;
    }
}
