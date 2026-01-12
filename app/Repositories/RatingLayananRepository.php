<?php

namespace App\Repositories;

use App\Models\RatingLayanan;
use App\Interfaces\Repositories\RatingLayananRepositoryInterface;

class RatingLayananRepository extends BaseRepository implements RatingLayananRepositoryInterface
{
    public function __construct(RatingLayanan $model)
    {
        $this->model = $model;
    }
}
