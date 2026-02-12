<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = DB::table('tbl_kategori')->get();

        foreach ($categories as $category) {
            $isWedding = $category->slug === 'pernikahan';

            // Layanan Fotografi / Combined
            DB::table('tbl_layanan')->updateOrInsert(
                [
                    'kategori_id' => $category->id,
                    'tipe' => 'fotografi',
                ],
                [
                    'nama' => $isWedding ? 'Wedding Photography & Videography' : 'Fotografi',
                    'deskripsi' => $isWedding ? 'Layanan dekorasi & dokumentasi dari MOROHENFILMS' : 'Layanan fotografi untuk ' . $category->nama,
                    'is_aktif' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            // Layanan Videografi (Optional/Extra)
            DB::table('tbl_layanan')->updateOrInsert(
                [
                    'kategori_id' => $category->id,
                    'tipe' => 'videografi',
                ],
                [
                    'nama' => $isWedding ? 'Cinematic Wedding Video' : 'Video Highlight',
                    'deskripsi' => 'Layanan video highlight untuk ' . $category->nama,
                    'is_aktif' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        $this->command->info('Layanan berhasil di-seed! (Fotografi & Videografi untuk setiap kategori)');
    }
}
