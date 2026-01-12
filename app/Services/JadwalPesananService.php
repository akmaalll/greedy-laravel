<?php

namespace App\Services;

use App\Repositories\JadwalPesananRepository;

class JadwalPesananService extends BaseService
{
    public function __construct(JadwalPesananRepository $repository)
    {
        parent::__construct($repository);
    }
}
