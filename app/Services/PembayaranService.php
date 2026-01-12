<?php

namespace App\Services;

use App\Repositories\PembayaranRepository;

class PembayaranService extends BaseService
{
    public function __construct(PembayaranRepository $repository)
    {
        parent::__construct($repository);
    }
}
