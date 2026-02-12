<?php

namespace App\Repositories;

use App\Models\RatingFotografer;
use App\Interfaces\Repositories\RatingFotograferRepositoryInterface;

class RatingFotograferRepository extends BaseRepository implements RatingFotograferRepositoryInterface
{
    public function __construct(RatingFotografer $model)
    {
        $this->model = $model;
    }
}
