<?php

namespace App\Services;

use App\Repositories\PenugasanFotograferRepository;

class PenugasanFotograferService extends BaseService
{
    public function __construct(PenugasanFotograferRepository $repository)
    {
        parent::__construct($repository);
    }
}
