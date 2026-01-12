<?php

namespace App\Services;

use App\Repositories\KetersediaanFotograferRepository;

class KetersediaanFotograferService extends BaseService
{
    public function __construct(KetersediaanFotograferRepository $repository)
    {
        parent::__construct($repository);
    }
}
