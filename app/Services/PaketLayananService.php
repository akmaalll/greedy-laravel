<?php

namespace App\Services;

use App\Repositories\PaketLayananRepository;

class PaketLayananService extends BaseService
{
    public function __construct(PaketLayananRepository $repository)
    {
        parent::__construct($repository);
    }
}
