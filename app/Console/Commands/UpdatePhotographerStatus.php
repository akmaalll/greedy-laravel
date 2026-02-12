<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UpdatePhotographerStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-photographer-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically update photographer availability status based on current assignments';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Running Real-Time Status Update...');

        $service = app(\App\Services\SchedulingService::class);
        $logs = $service->updateRealTimeStatus();

        foreach ($logs as $log) {
            $this->info($log);
        }

        $this->info('Status update completed.');
    }
}
