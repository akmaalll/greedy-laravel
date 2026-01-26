<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class BusyDaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('📅 Generating Busy Day Scenario (14 Feb 2026)...');

        $date = '2026-02-14'; // Valentine's Busy Day
        $photographers = \App\Models\User::where('role_id', 4)->take(5)->get();
        $client = \App\Models\User::where('role_id', 3)->first();
        $package = \App\Models\PaketLayanan::first();

        if ($photographers->count() < 5) {
            $this->command->warn("Need 5 photographers for best scenario. Found {$photographers->count()}. Proceeding anyway.");
        }

        // Scenario:
        // P1: Very Busy (2 Orders) -> High Penalty
        // P2: Busy (1 Order) -> Medium Penalty
        // P3: Busy (1 Order) -> Medium Penalty
        // P4: Busy (1 Order) -> Medium Penalty
        // P5: Free (0 Orders) -> The Target for Greedy Algorithm

        // Ensure Availability exists for this date
        foreach ($photographers as $p) {
            \App\Models\KetersediaanFotografer::updateOrCreate(
                ['fotografer_id' => $p->id, 'tanggal' => $date],
                ['status' => 'tersedia', 'waktu_mulai' => '08:00:00', 'waktu_selesai' => '22:00:00']
            );
        }

        // --- P1 (Very Busy - 2 Orders) ---
        $this->createOrder($client, $package, $photographers[0], $date, '08:00:00', '10:00:00');
        $this->createOrder($client, $package, $photographers[0], $date, '13:00:00', '15:00:00');

        // --- P2, P3, P4 (Busy - 1 Order each) ---
        $this->createOrder($client, $package, $photographers[1], $date, '09:00:00', '11:00:00');
        $this->createOrder($client, $package, $photographers[2], $date, '10:00:00', '12:00:00');
        $this->createOrder($client, $package, $photographers[3], $date, '14:00:00', '16:00:00');

        // --- P5 Left Free ---

        $this->command->info("✅ Scenario Created for $date!");
        $this->command->info("   - {$photographers[0]->name}: 2 Orders (Heavy Load)");
        $this->command->info("   - {$photographers[1]->name}: 1 Order");
        $this->command->info("   - {$photographers[2]->name}: 1 Order");
        $this->command->info("   - {$photographers[3]->name}: 1 Order");
        $p5Name = isset($photographers[4]) ? $photographers[4]->name : 'N/A';
        $this->command->info("   - {$p5Name}: 0 Orders (Target Candidate)");

        $this->command->info("\n👉 Run 'php artisan export:greedy-data --date=$date' to see the calculation!");
    }

    private function createOrder($client, $package, $photographer, $date, $start, $end)
    {
        $order = \App\Models\Pesanan::create([
            'nomor_pesanan' => 'BUSY-'.strtoupper(\Illuminate\Support\Str::random(5)),
            'klien_id' => $client->id,
            'tanggal_acara' => $date,
            'waktu_mulai_acara' => $start,
            'waktu_selesai_acara' => $end,
            'lokasi_acara' => 'Busy Day Location',
            'status' => 'dikonfirmasi', // Active assignment
            'subtotal' => 100000,
            'total_harga' => 100000,
            'catatan' => 'Scenario Busy Day',
        ]);

        \App\Models\ItemPesanan::create([
            'pesanan_id' => $order->id,
            'paket_layanan_id' => $package->id ?? 1,
            'jumlah' => 1,
            'harga_satuan' => 100000,
            'subtotal' => 100000,
        ]);

        \App\Models\PenugasanFotografer::create([
            'pesanan_id' => $order->id,
            'fotografer_id' => $photographer->id,
            'status' => 'diterima',
            'waktu_ditugaskan' => now(),
        ]);
    }
}
