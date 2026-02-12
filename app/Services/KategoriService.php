<?php

namespace App\Services;

use App\Repositories\KategoriRepository;

class KategoriService extends BaseService
{
    public function __construct(KategoriRepository $repository)
    {
        parent::__construct($repository);
    }
}
