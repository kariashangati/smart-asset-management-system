<?php

namespace App\Console\Commands;

use App\Models\Asset;
use Illuminate\Console\Command;

class CleanupOldLocationLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'location:cleanup {--days=30 : Delete logs older than N days}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleanup old location logs from the database';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $days = $this->option('days');
        $cutoffDate = now()->subDays($days);

        $deletedCount = \App\Models\LocationLog::where('recorded_at', '<', $cutoffDate)->delete();

        $this->info("Deleted {$deletedCount} location logs older than {$days} days.");
        return self::SUCCESS;
    }
}
