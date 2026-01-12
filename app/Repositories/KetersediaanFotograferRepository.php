<?php

namespace App\Repositories;

use App\Models\KetersediaanFotografer;
use App\Interfaces\Repositories\KetersediaanFotograferRepositoryInterface;

class KetersediaanFotograferRepository extends BaseRepository implements KetersediaanFotograferRepositoryInterface
{
    public function __construct(KetersediaanFotografer $model)
    {
        $this->model = $model;
    }
}
