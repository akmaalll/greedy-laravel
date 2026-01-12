<?php

namespace App\Services;

use App\Repositories\RatingFotograferRepository;

class RatingFotograferService extends BaseService
{
    public function __construct(RatingFotograferRepository $repository)
    {
        parent::__construct($repository);
    }
}
