<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LiveTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🎬 Creating Live Test Scenarios for Status Update...');

        $now = \Carbon\Carbon::now();
        $date = $now->toDateString();
        
        // 1. Get 3 Photographers
        $photographers = \App\Models\User::where('role_id', 4)->take(3)->get();
        if ($photographers->count() < 3) {
            $this->command->error('❌ Need at least 3 photographers. Run UserSeeder first.');
            return;
        }

        $p1 = $photographers[0]; // Case 1: Just Finished (Should be Tersedia)
        $p2 = $photographers[1]; // Case 2: Running Now (Should be Dipesan)
        $p3 = $photographers[2]; // Case 3: Future Today (Should be Tersedia)

        // 2. Ensure they are available today in Ketersediaan
        foreach ($photographers as $p) {
            \App\Models\KetersediaanFotografer::updateOrCreate(
                ['fotografer_id' => $p->id, 'tanggal' => $date],
                ['status' => 'tersedia', 'waktu_mulai' => '08:00:00', 'waktu_selesai' => '22:00:00']
            );
        }

        $this->command->info("✅ Availability set for today ($date).");

        $client = \App\Models\User::where('role_id', 3)->first();
        $package = \App\Models\PaketLayanan::first();

        // --- SCENARIO 1: Finished 10 mins ago ---
        // E.g. Now 12:40. Order was 12:00 - 12:30.
        // Status should be 'dikonfirmasi' (not formally 'selesai' by admin yet), but TIME is passed.
        $start1 = $now->copy()->subMinutes(40);
        $end1 = $now->copy()->subMinutes(10);
        
        $order1 = $this->createOrder($client, $package, $p1, $date, $start1, $end1, 'SCENARIO 1: Finished 10 mins ago');
        $this->command->info("👉 Created Scenario 1 (Finished): Photographer {$p1->name}. Expect: TERSEDIA.");


        // --- SCENARIO 2: Running Now ---
        // E.g. Now 12:40. Order is 12:30 - 13:00.
        $start2 = $now->copy()->subMinutes(10);
        $end2 = $now->copy()->addMinutes(20);

        $order2 = $this->createOrder($client, $package, $p2, $date, $start2, $end2, 'SCENARIO 2: Running Now');
        // Manually set initial status to 'dipesan' to simulate that it was booked
        \App\Models\KetersediaanFotografer::where('fotografer_id', $p2->id)->whereDate('tanggal', $date)->update(['status' => 'dipesan']);
        $this->command->info("👉 Created Scenario 2 (Running): Photographer {$p2->name}. Expect: DIPESAN.");


        // --- SCENARIO 3: Future Today ---
        // E.g. Now 12:40. Order starts 14:00.
        $start3 = $now->copy()->addHours(2);
        $end3 = $now->copy()->addHours(3);

        $order3 = $this->createOrder($client, $package, $p3, $date, $start3, $end3, 'SCENARIO 3: Future Today');
        // Future booking usually sets availability to 'dipesan' initially?
        // Let's set it to 'dipesan' to see if the command FIXES it back to 'tersedia' until the time comes.
        // Note: The previous logic of Greedy sets it to 'dipesan' immediately.
        \App\Models\KetersediaanFotografer::where('fotografer_id', $p3->id)->whereDate('tanggal', $date)->update(['status' => 'dipesan']);
        $this->command->info("👉 Created Scenario 3 (Future): Photographer {$p3->name}. Expect: TERSEDIA (until time comes).");

        $this->command->info("\n⚠️  Run 'php artisan app:update-photographer-status' to test the logic!");
    }

    private function createOrder($client, $package, $photographer, $date, $start, $end, $note)
    {
        $order = \App\Models\Pesanan::create([
            'nomor_pesanan' => 'LIVE-'.strtoupper(\Illuminate\Support\Str::random(5)),
            'klien_id' => $client->id,
            'tanggal_acara' => $date,
            'waktu_mulai_acara' => $start->format('H:i:s'),
            'waktu_selesai_acara' => $end->format('H:i:s'),
            'lokasi_acara' => 'Live Test Location',
            'status' => 'dikonfirmasi', // Active status
            'subtotal' => 100000,
            'total_harga' => 100000,
            'catatan' => $note
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
            'status' => 'diterima', // Active assignment
            'waktu_ditugaskan' => $start, // just dummy
        ]);

        return $order;
    }
}
