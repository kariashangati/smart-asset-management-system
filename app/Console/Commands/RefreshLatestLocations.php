<?php

namespace App\Console\Commands;

use App\Models\Asset;
use App\Models\AssetLatestLocation;
use App\Models\LocationLog;
use Illuminate\Console\Command;

class RefreshLatestLocations extends Command
{
    protected $signature = 'tracking:refresh-latest';
    protected $description = 'Update asset_latest_locations from latest location logs';

    public function handle()
    {
        $assets = Asset::has('locationLogs')->get();
        foreach ($assets as $asset) {
            $latestLog = LocationLog::where('asset_id', $asset->id)->latest('recorded_at')->first();
            if ($latestLog) {
                AssetLatestLocation::updateOrCreate(
                    ['asset_id' => $asset->id],
                    [
                        'tracker_device_id' => $latestLog->tracker_device_id,
                        'latitude' => $latestLog->latitude,
                        'longitude' => $latestLog->longitude,
                        'last_motion_detected' => $latestLog->motion_detected,
                        'last_recorded_at' => $latestLog->recorded_at,
                    ]
                );
            }
        }
        $this->info('Latest locations refreshed.');
    }
}