<?php

namespace App\Services;

use App\Repositories\RatingLayananRepository;

class RatingLayananService extends BaseService
{
    public function __construct(RatingLayananRepository $repository)
    {
        parent::__construct($repository);
    }
}
