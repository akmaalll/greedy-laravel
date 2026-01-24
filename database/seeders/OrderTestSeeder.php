<?php

namespace Database\Seeders;

use App\Models\ItemPesanan;
use App\Models\KetersediaanFotografer;
use App\Models\PaketLayanan;
use App\Models\Pesanan;
use App\Models\RatingFotografer;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates 30 test orders to test the greedy algorithm.
     */
    public function run(): void
    {
        DB::beginTransaction();
        try {
            $this->command->info('🚀 Starting Order Test Seeder (Makassar Edition - Clean Slate)...');

            // 0. Cleanup existing order data
            $this->command->info('🧹 Cleaning up existing order-related data...');
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('tbl_rating_fotografer')->delete();
            DB::table('tbl_rating_layanan')->delete();
            DB::table('tbl_pembayaran')->delete();
            DB::table('tbl_item_pesanan')->delete();
            DB::table('tbl_penugasan_fotografer')->delete();
            DB::table('tbl_pesanan')->delete();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            // 1. Get roles
            // Based on earlier check, IDs are 3 for client, 4 for photographer
            $clientRole = DB::table('roles')->where('slug', 'client')->first();
            $photographerRole = DB::table('roles')->where('slug', 'photographer')->first();

            if (! $clientRole || ! $photographerRole) {
                $this->command->error('❌ Roles not found! Check slugs.');
                DB::rollBack();

                return;
            }

            $clientRoleId = $clientRole->id;
            $photographerRoleId = $photographerRole->id;

            // 2. Get existing clients (excluding client1 and client2)
            $this->command->info('📝 Getting eligible clients...');
            $clients = User::where('role_id', $clientRoleId)
                ->whereNotIn('email', ['client1@example.com', 'client2@example.com'])
                ->get();

            if ($clients->isEmpty()) {
                $this->command->error('❌ No suitable clients found!');
                DB::rollBack();

                return;
            }

            $this->command->info("   Found {$clients->count()} eligible clients.");

            // 3. Get photographers
            $this->command->info('👨‍💼 Getting photographers...');
            $photographers = User::where('role_id', $photographerRoleId)->get();

            if ($photographers->isEmpty()) {
                $this->command->error('❌ No photographers found!');
                DB::rollBack();

                return;
            }

            $this->command->info("   Found {$photographers->count()} photographers.");

            // 4. Create ratings for photographers
            $this->command->info('⭐ Creating photographer ratings...');
            $this->createPhotographerRatings($photographers, $clients);

            // 5. Get available packages
            $packages = PaketLayanan::where('is_aktif', true)->get();
            if ($packages->isEmpty()) {
                $packages = PaketLayanan::all();
            }

            if ($packages->isEmpty()) {
                $this->command->error('❌ No packages found!');
                DB::rollBack();

                return;
            }

            // 6. Define January 2026 dates (Only include dates that HAVE availability and are NOT Sundays)
            $availableDates = KetersediaanFotografer::whereMonth('tanggal', 1)
                ->whereYear('tanggal', 2026)
                ->where('status', 'tersedia')
                ->distinct()
                ->pluck('tanggal')
                ->map(fn ($date) => \Carbon\Carbon::parse($date)->format('Y-m-d'))
                ->toArray();

            if (empty($availableDates)) {
                $this->command->error('❌ No availability records found for January 2026! Please generate availability first.');
                DB::rollBack();

                return;
            }

            $this->command->info('   Found '.count($availableDates).' available dates for orders.');

            // 7. Create 30 test orders
            $this->command->info('📦 Creating 30 test orders with random packages for matching dates...');
            $this->createTestOrders($clients, $packages, $availableDates);

            DB::commit();
            $this->command->info('✅ Order Test Seeder completed successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('❌ Error: '.$e->getMessage());
            throw $e;
        }
    }

    /**
     * Create ratings for photographers using past dummy orders
     */
    private function createPhotographerRatings($photographers, $clients)
    {
        $ratings = [5, 4, 3, 5, 4, 5]; // Random selection pool

        foreach ($photographers as $photographer) {
            $ratingCount = rand(3, 6);

            for ($i = 0; $i < $ratingCount; $i++) {
                $client = $clients->random();

                // Past event for rating purposes
                $order = Pesanan::create([
                    'nomor_pesanan' => 'PREV-'.Str::random(10),
                    'klien_id' => $client->id,
                    'tanggal_acara' => Carbon::now()->subMonths(rand(1, 12)),
                    'waktu_mulai_acara' => '09:00:00',
                    'waktu_selesai_acara' => '11:00:00',
                    'lokasi_acara' => 'Previous Location',
                    'status' => 'selesai',
                    'subtotal' => 0,
                    'total_harga' => 0,
                ]);

                RatingFotografer::create([
                    'pesanan_id' => $order->id,
                    'fotografer_id' => $photographer->id,
                    'klien_id' => $client->id,
                    'rating' => $ratings[array_rand($ratings)],
                    'ulasan' => 'Professional photographer, very satisfied with the results.',
                ]);
            }
        }
    }

    /**
     * Create 30 test orders
     */
    private function createTestOrders($clients, $packages, $availableDates)
    {
        $orderStatuses = ['menunggu', 'dikonfirmasi'];
        $locations = [
            'Pantai Losari', 'Benteng Rotterdam', 'Trans Studio Makassar',
            'Bugis Waterpark Adventure', 'Karebosi Link', 'Nipah Mall Makassar',
            'Phinisi Point (PiPo)', 'Gedung UpperHills Makassar', 'Hotel Claro Makassar',
            'Masjid Terapung Amirul Mukminin', 'Benteng Somba Opu', 'Pulau Lae-Lae',
            'Lapadde Coffee Makassar', 'Mal Panakkukang',
        ];

        $times = [
            ['08:00:00', '10:00:00'], ['10:00:00', '12:00:00'],
            ['13:00:00', '15:00:00'], ['15:00:00', '17:00:00'],
        ];

        for ($i = 1; $i <= 30; $i++) {
            $client = $clients->random();
            $date = $availableDates[array_rand($availableDates)];
            $timeSlot = $times[array_rand($times)];
            $package = $packages->random();

            // Calculate end time based on package duration
            $startTime = Carbon::parse($date . ' ' . $timeSlot[0]);
            if ($package->tipe_durasi == 'jam') {
                $endTime = $startTime->copy()->addHours($package->nilai_durasi);
            } else {
                $endTime = $startTime->copy()->addMinutes($package->nilai_durasi);
            }

            $order = Pesanan::create([
                'nomor_pesanan' => 'TEST-'.str_pad($i, 5, '0', STR_PAD_LEFT),
                'klien_id' => $client->id,
                'tanggal_acara' => $date,
                'waktu_mulai_acara' => $timeSlot[0],
                'waktu_selesai_acara' => $endTime->format('H:i:s'),
                'lokasi_acara' => $locations[array_rand($locations)],
                'deskripsi_acara' => 'Test event photoshoot #'.$i,
                'status' => $orderStatuses[array_rand($orderStatuses)],
                'subtotal' => $package->harga_dasar,
                'total_harga' => $package->harga_dasar,
                'catatan' => 'Greedy algorithm test case with '.$package->nama . ' (' . $package->nilai_durasi . ' ' . $package->tipe_durasi . ')',
            ]);

            ItemPesanan::create([
                'pesanan_id' => $order->id,
                'paket_layanan_id' => $package->id,
                'jumlah' => 1,
                'harga_satuan' => $package->harga_dasar,
                'subtotal' => $package->harga_dasar,
            ]);
        }
    }
}
