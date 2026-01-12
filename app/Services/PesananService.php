<?php

namespace App\Services;

use App\Repositories\PesananRepository;
use Illuminate\Support\Str;
use DB;

class PesananService extends BaseService
{
    public function __construct(PesananRepository $repository)
    {
        parent::__construct($repository);
    }

    public function createPesanan(array $data, array $items)
    {
        return DB::transaction(function () use ($data, $items) {
            // Generate order number if not provided
            if (!isset($data['nomor_pesanan'])) {
                $count = $this->repository->all()->count() + 1;
                $data['nomor_pesanan'] = 'ORD-' . date('Ymd') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
            }

            $pesanan = $this->repository->create($data);

            $subtotal = 0;
            foreach ($items as $item) {
                $subtotal += $item['subtotal'];
                $pesanan->itemPesanan()->create($item);
            }

            $pesanan->update([
                'subtotal' => $subtotal,
                'total_harga' => $subtotal + ($data['biaya_overtime'] ?? 0)
            ]);

            return $pesanan;
        });
    }
}
