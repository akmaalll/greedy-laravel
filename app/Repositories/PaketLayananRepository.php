<?php

namespace App\Repositories;

use App\Models\PaketLayanan;
use App\Interfaces\Repositories\PaketLayananRepositoryInterface;

class PaketLayananRepository extends BaseRepository implements PaketLayananRepositoryInterface
{
    public function __construct(PaketLayanan $model)
    {
        $this->model = $model;
    }
}
