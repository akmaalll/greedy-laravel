<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'nama' => 'Pernikahan',
                'slug' => 'pernikahan',
                'deskripsi' => 'Layanan fotografi dan videografi untuk acara pernikahan',
                'is_aktif' => true,
            ],
            [
                'nama' => 'Prewedding',
                'slug' => 'prewedding',
                'deskripsi' => 'Layanan fotografi dan videografi untuk sesi prewedding',
                'is_aktif' => true,
            ],
            [
                'nama' => 'Potret',
                'slug' => 'potret',
                'deskripsi' => 'Layanan fotografi potret personal atau profesional',
                'is_aktif' => true,
            ],
            [
                'nama' => 'Keluarga',
                'slug' => 'keluarga',
                'deskripsi' => 'Layanan fotografi dan videografi untuk acara keluarga',
                'is_aktif' => true,
            ],
            [
                'nama' => 'Acara / Event',
                'slug' => 'acara-event',
                'deskripsi' => 'Layanan fotografi dan videografi untuk berbagai acara dan event',
                'is_aktif' => true,
            ],
        ];

        foreach ($categories as $category) {
            DB::table('tbl_kategori')->updateOrInsert(
                ['slug' => $category['slug']],
                [
                    'nama' => $category['nama'],
                    'deskripsi' => $category['deskripsi'],
                    'is_aktif' => $category['is_aktif'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        $this->command->info('5 kategori berhasil di-seed!');
    }
}
