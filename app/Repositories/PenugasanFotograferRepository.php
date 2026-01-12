<?php

namespace App\Repositories;

use App\Models\PenugasanFotografer;
use App\Interfaces\Repositories\PenugasanFotograferRepositoryInterface;

class PenugasanFotograferRepository extends BaseRepository implements PenugasanFotograferRepositoryInterface
{
    public function __construct(PenugasanFotografer $model)
    {
        $this->model = $model;
    }
}
