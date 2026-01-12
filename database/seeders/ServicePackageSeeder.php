<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServicePackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all categories and services
        $pernikahan = DB::table('tbl_kategori')->where('slug', 'pernikahan')->first();
        $prewedding = DB::table('tbl_kategori')->where('slug', 'prewedding')->first();
        $potret = DB::table('tbl_kategori')->where('slug', 'potret')->first();
        $keluarga = DB::table('tbl_kategori')->where('slug', 'keluarga')->first();
        $acaraEvent = DB::table('tbl_kategori')->where('slug', 'acara-event')->first();

        // ============================================
        // 1. PERNIKAHAN
        // ============================================
        // Pernikahan (MOROHENFILMS Special Offer)
        $pernikahanFoto = DB::table('tbl_layanan')->where('kategori_id', $pernikahan->id)->where('tipe', 'fotografi')->first();

        // Basic Package
        $this->createPackage(
            $pernikahanFoto->id,
            'Basic Package',
            'jam',
            8,
            5500000,
            null,
            null,
            'Paket Pernikahan Dasar dari MOROHENFILMS',
            [
                '1 Photographer - 1 Videographer',
                'Unlimited Photo & All File in Flashdisk',
                'Cinematic Video max. 3 Minute',
                '1 Wedding Album Magnetic 10 Sheet 35x35cm'
            ]
        );

        // Standard Package
        $this->createPackage(
            $pernikahanFoto->id,
            'Standard Package',
            'jam',
            8,
            7500000,
            null,
            null,
            'Paket Pernikahan Standar dari MOROHENFILMS',
            [
                '1 Photographer - 2 Videographer',
                'Unlimited Photo & All File in Flashdisk',
                'Cinematic Video max. 3-4 Minute',
                'Documentation Wedding Video 30-60 Minute',
                '2 Wedding Album Magnetic 10 Sheet 35x35cm'
            ]
        );

        // Exclusive Package
        $this->createPackage(
            $pernikahanFoto->id,
            'Exclusive Package',
            'jam',
            8,
            12000000,
            null,
            null,
            'Paket Pernikahan Eksklusif dari MOROHENFILMS',
            [
                '2 Photographer - 2 Videographer',
                'Unlimited Photo & All File in Flashdisk',
                'Cinematic Video max. 3-4 Minute',
                'Documentation Wedding Video 30-60 Minute',
                '2 Wedding Album Collage 25x30cm with Box Premium',
                '1 Print & Frame Photo 20rw (50x75cm)',
                'Free Photo Engagement & Prewedding'
            ]
        );

        // ============================================
        // 2. PREWEDDING
        // ============================================
        $preweddingFoto = DB::table('tbl_layanan')->where('kategori_id', $prewedding->id)->where('tipe', 'fotografi')->first();
        $preweddingVideo = DB::table('tbl_layanan')->where('kategori_id', $prewedding->id)->where('tipe', 'videografi')->first();

        // Fotografi Prewedding
        $this->createPackage($preweddingFoto->id, '2 Jam', 'jam', 2, 1500000);
        $this->createPackage($preweddingFoto->id, '4 Jam', 'jam', 4, 2500000);
        $this->createPackage($preweddingFoto->id, '6 Jam', 'jam', 6, 3500000);

        // Video Highlight Prewedding
        $this->createPackage($preweddingVideo->id, '2-3 Menit', 'menit', 3, 1500000);
        $this->createPackage($preweddingVideo->id, '3-4 Menit', 'menit', 4, 2500000);
        $this->createPackage($preweddingVideo->id, '4-6 Menit', 'menit', 6, 3500000);

        // ============================================
        // 3. POTRET
        // ============================================
        $potretFoto = DB::table('tbl_layanan')->where('kategori_id', $potret->id)->where('tipe', 'fotografi')->first();
        $potretVideo = DB::table('tbl_layanan')->where('kategori_id', $potret->id)->where('tipe', 'videografi')->first();

        // Fotografi Potret
        $this->createPackage($potretFoto->id, '5 Foto Edit', 'foto', 5, 500000, 100000, 'foto', 'Pemotretan ±1-2 jam, editing & file digital');
        $this->createPackage($potretFoto->id, '10 Foto Edit', 'foto', 10, 900000, 100000, 'foto', 'Pemotretan ±1-2 jam, editing & file digital');
        $this->createPackage($potretFoto->id, '20 Foto Edit', 'foto', 20, 1600000, 100000, 'foto', 'Pemotretan ±1-2 jam, editing & file digital');

        // Video Highlight Potret
        $this->createPackage($potretVideo->id, '1-2 Menit', 'menit', 2, 750000);
        $this->createPackage($potretVideo->id, '2-3 Menit', 'menit', 3, 1200000);
        $this->createPackage($potretVideo->id, '3-4 Menit', 'menit', 4, 2000000);

        // ============================================
        // 4. KELUARGA
        // ============================================
        $keluargaFoto = DB::table('tbl_layanan')->where('kategori_id', $keluarga->id)->where('tipe', 'fotografi')->first();
        $keluargaVideo = DB::table('tbl_layanan')->where('kategori_id', $keluarga->id)->where('tipe', 'videografi')->first();

        // Fotografi Keluarga
        $this->createPackage($keluargaFoto->id, '1 Jam', 'jam', 1, 600000);
        $this->createPackage($keluargaFoto->id, '2 Jam', 'jam', 2, 1000000);
        $this->createPackage($keluargaFoto->id, '4 Jam', 'jam', 4, 1800000);

        // Video Highlight Keluarga
        $this->createPackage($keluargaVideo->id, '1-2 Menit', 'menit', 2, 900000);
        $this->createPackage($keluargaVideo->id, '2-3 Menit', 'menit', 3, 1400000);
        $this->createPackage($keluargaVideo->id, '3-4 Menit', 'menit', 4, 2300000);

        // ============================================
        // 5. ACARA / EVENT
        // ============================================
        $acaraFoto = DB::table('tbl_layanan')->where('kategori_id', $acaraEvent->id)->where('tipe', 'fotografi')->first();
        $acaraVideo = DB::table('tbl_layanan')->where('kategori_id', $acaraEvent->id)->where('tipe', 'videografi')->first();

        // Fotografi Acara/Event
        $this->createPackage($acaraFoto->id, '2 Jam', 'jam', 2, 1000000);
        $this->createPackage($acaraFoto->id, '4 Jam', 'jam', 4, 2000000);
        $this->createPackage($acaraFoto->id, '6 Jam', 'jam', 6, 3000000);
        $this->createPackage($acaraFoto->id, '8 Jam', 'jam', 8, 4000000);

        // Video Highlight Acara/Event
        $this->createPackage($acaraVideo->id, '3-4 Menit', 'menit', 4, 1800000);
        $this->createPackage($acaraVideo->id, '4-6 Menit', 'menit', 6, 3000000);
        $this->createPackage($acaraVideo->id, '6-8 Menit', 'menit', 8, 4200000);
        $this->createPackage($acaraVideo->id, '8-10 Menit', 'menit', 10, 5500000);

        $this->command->info('Paket layanan berhasil di-seed! Total paket dibuat.');
    }

    /**
     * Helper function to create service package
     */
    private function createPackage(
        int $layananId,
        string $nama,
        string $tipeDurasi,
        int $nilaiDurasi,
        float $hargaDasar,
        ?float $hargaOvertime = null,
        ?string $satuanOvertime = null,
        ?string $deskripsi = null,
        array $fitur = []
    ): void {
        DB::table('tbl_paket_layanan')->updateOrInsert(
            [
                'layanan_id' => $layananId,
                'nama' => $nama,
            ],
            [
                'deskripsi' => $deskripsi,
                'tipe_durasi' => $tipeDurasi,
                'nilai_durasi' => $nilaiDurasi,
                'harga_dasar' => $hargaDasar,
                'harga_overtime' => $hargaOvertime,
                'satuan_overtime' => $satuanOvertime,
                'fitur' => json_encode($fitur),
                'is_aktif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
