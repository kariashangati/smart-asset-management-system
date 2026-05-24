<?php

namespace App\Console\Commands;

use App\Models\LocationLog;
use App\Events\AssetLocationUpdated;
use Illuminate\Console\Command;

class ProcessLocationUpdates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'location:process {--minutes=5 : Process logs from the last N minutes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process pending location logs and trigger events';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $minutes = $this->option('minutes');
        $since = now()->subMinutes($minutes);

        $logs = LocationLog::where('recorded_at', '>=', $since)
            ->where('processed', false)
            ->with('asset.trackerDevice')
            ->get();

        if ($logs->isEmpty()) {
            $this->info('No location logs to process.');
            return self::SUCCESS;
        }

        foreach ($logs as $log) {
            // Dispatch event to trigger geofence checks
            AssetLocationUpdated::dispatch(
                asset: $log->asset,
                latitude: $log->latitude,
                longitude: $log->longitude,
                speed: $log->speed,
                motionDetected: $log->motion_detected
            );

            // Mark as processed
            $log->update(['processed' => true]);
        }

        $this->info("Processed {$logs->count()} location logs.");
        return self::SUCCESS;
    }
}
