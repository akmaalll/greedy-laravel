<?php

namespace App\Services;

use App\Repositories\LayananRepository;

class LayananService extends BaseService
{
    public function __construct(LayananRepository $repository)
    {
        parent::__construct($repository);
    }
}
