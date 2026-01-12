<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\KetersediaanFotografer;
use App\Models\PenugasanFotografer;
use App\Models\RatingFotografer;
use App\Models\Pesanan;
use App\Models\PaketLayanan;
use App\Models\ItemPesanan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GreedyTestSeeder extends Seeder
{
    public function run(): void
    {
        $today = Carbon::today()->format('Y-m-d');
        
        // 1. Get/Create Client
        $client = User::whereRole('client')->first();
        if (!$client) {
            $client = User::create([
                'name' => 'Demo Client',
                'email' => 'client@greedy.test',
                'password' => bcrypt('password'),
                'role_id' => DB::table('roles')->where('slug', 'client')->first()->id
            ]);
        }

        // 2. Identify existing Photographers from UserSeeder
        $photoA = User::where('email', 'photographer1@example.com')->first();
        $photoB = User::where('email', 'photographer2@example.com')->first();
        $photoC = User::where('email', 'photographer3@example.com')->first();
        
        if (!$photoA || !$photoB || !$photoC) {
            $this->command->error('Default photographers from UserSeeder not found. Please run php artisan db:seed --class=UserSeeder first.');
            return;
        }

        // Update names for clear testing (Optional but helpful)
        $photoA->update(['name' => 'Andi Wijaya (High Rating, Busy)']);
        $photoB->update(['name' => 'Dewi Lestari (High Rating, Free)']);
        $photoC->update(['name' => 'Rudi Hermawan (Low Rating, Free)']);

        // Photographer D is not used because we only have 3 from UserSeeder
        $photoD = null;

        // 3. Clear old test data for clean test
        KetersediaanFotografer::whereIn('fotografer_id', [$photoA->id, $photoB->id, $photoC->id])->delete();
        
        // 4. Set Availability Scenario
        // Andi: FULL
        KetersediaanFotografer::create([
            'fotografer_id' => $photoA->id,
            'tanggal' => $today,
            'waktu_mulai' => '08:00',
            'waktu_selesai' => '17:00',
            'status' => 'dipesan'
        ]);

        // Dewi: Available 08:00 - 17:00 (The Backup)
        KetersediaanFotografer::create([
            'fotografer_id' => $photoB->id,
            'tanggal' => $today,
            'waktu_mulai' => '08:00',
            'waktu_selesai' => '17:00',
            'status' => 'tersedia'
        ]);

        // Rudi: ONLY available 13:00 - 14:00 (The Gap)
        KetersediaanFotografer::create([
            'fotografer_id' => $photoC->id,
            'tanggal' => $today,
            'waktu_mulai' => '13:00',
            'waktu_selesai' => '14:00',
            'status' => 'tersedia'
        ]);

        // 5. Setup Ratings & Workload (Need real orders due to FK)
        // Andi Rating 5
        $orderA = Pesanan::firstOrCreate(['nomor_pesanan' => 'GRD-RAT-A'], [
            'klien_id' => $client->id, 'tanggal_acara' => $today,
            'waktu_mulai_acara' => '08:00', 'lokasi_acara' => 'Test', 'status' => 'selesai', 'subtotal' => 0, 'total_harga' => 0
        ]);
        RatingFotografer::firstOrCreate(['pesanan_id' => $orderA->id, 'fotografer_id' => $photoA->id], ['klien_id' => $client->id, 'rating' => 5]);

        // Dewi Rating 5
        $orderB = Pesanan::firstOrCreate(['nomor_pesanan' => 'GRD-RAT-B'], [
            'klien_id' => $client->id, 'tanggal_acara' => $today,
            'waktu_mulai_acara' => '08:00', 'lokasi_acara' => 'Test', 'status' => 'selesai', 'subtotal' => 0, 'total_harga' => 0
        ]);
        RatingFotografer::firstOrCreate(['pesanan_id' => $orderB->id, 'fotografer_id' => $photoB->id], ['klien_id' => $client->id, 'rating' => 5]);

        // Rudi Rating 2 (Low)
        $orderC = Pesanan::firstOrCreate(['nomor_pesanan' => 'GRD-RAT-C'], [
            'klien_id' => $client->id, 'tanggal_acara' => $today,
            'waktu_mulai_acara' => '08:00', 'lokasi_acara' => 'Test', 'status' => 'selesai', 'subtotal' => 0, 'total_harga' => 0
        ]);
        RatingFotografer::firstOrCreate(['pesanan_id' => $orderC->id, 'fotografer_id' => $photoC->id], ['klien_id' => $client->id, 'rating' => 2]);

        $this->command->info('Greedy Test Seeder Finished!');
        $this->command->info('Scenario: Andi is FULL. Dewi is AVAILABLE (08:00-17:00). Rudi ONLY free (13:00-14:00).');
        $this->command->info('Expected Result: Dewi should be picked for general orders. Rudi for specific 13:00 gap.');
    }
}
