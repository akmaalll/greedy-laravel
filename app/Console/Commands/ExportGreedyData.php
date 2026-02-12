<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ExportGreedyData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'export:greedy-data {--date= : The date Y-m-d to analyze}';

    protected $description = 'Export greedy algorithm calculation data to CSV';

    public function handle()
    {
        $dateParam = $this->option('date') ?? \Carbon\Carbon::now()->toDateString();
        $this->info("📊 Generating Greedy Algorithm Data for {$dateParam}...");

        $photographers = \App\Models\User::where('role_id', 4)
            ->withAvg('ratingDiterima', 'rating')
            ->get();

        $filename = "greedy_data_{$dateParam}.csv";
        $file = fopen($filename, 'w');

        // CSV Header
        fputcsv($file, ['ID', 'Nama Fotografer', 'Rata-rata Rating', 'Beban Kerja (Tugas Hari Ini)', 'Skor Greedy', 'Status Ketersediaan Hari Ini']);

        $count = 0;
        foreach ($photographers as $p) {
            // Calculate variables
            $rating = round($p->rating_diterima_avg_rating ?? 0, 2);
            
            // Workload: Active assignments ON SELECTED DATE
            $workload = \App\Models\PenugasanFotografer::where('fotografer_id', $p->id)
                ->whereHas('pesanan', function($q) use ($dateParam) {
                    $q->whereDate('tanggal_acara', $dateParam)
                      ->whereIn('status', ['menunggu', 'dikonfirmasi', 'berlangsung']);
                })
                ->where('status', '!=', 'dibatalkan')
                ->count();

            // Availability Status ON SELECTED DATE
            $avail = \App\Models\KetersediaanFotografer::where('fotografer_id', $p->id)
                ->whereDate('tanggal', $dateParam)
                ->first();
            $status = $avail ? $avail->status : 'Tidak Ada Jadwal';

            // Greedy Score Formula: Rating - (Workload * 2)
            $score = $rating - ($workload * 2);

            fputcsv($file, [
                $p->id,
                $p->name,
                $rating,
                $workload,
                $score,
                $status,
            ]);
            $count++;
        }

        fclose($file);

        $this->info("✅ Export finished! {$count} rows written to '{$filename}'.");
        $this->info("You can open '{$filename}' in Excel or Google Sheets for your report.");
    }
}
