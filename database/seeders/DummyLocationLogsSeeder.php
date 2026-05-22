<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\AssetDeviceAssignment;
use App\Models\LocationLog;
use Illuminate\Database\Seeder;

class DummyLocationLogsSeeder extends Seeder
{
    public function run()
    {
        // Only run if there are assignments
        $assignments = AssetDeviceAssignment::where('is_active', true)->get();

        foreach ($assignments as $assignment) {
            $asset = $assignment->asset;
            $device = $assignment->trackerDevice;

            // Generate 10-50 random locations around a base point (e.g., institute coordinates)
            $baseLat = -6.792354;
            $baseLng = 39.208328;

            for ($i = 0; $i < rand(10, 50); $i++) {
                $lat = $baseLat + (rand(-500, 500) / 100000);
                $lng = $baseLng + (rand(-500, 500) / 100000);
                $recordedAt = now()->subMinutes(rand(0, 1440))->subDays(rand(0, 30));

                LocationLog::create([
                    'tracker_device_id' => $device->id,
                    'asset_id' => $asset->id,
                    'latitude' => $lat,
                    'longitude' => $lng,
                    'speed' => rand(0, 120),
                    'motion_detected' => rand(0, 1),
                    'recorded_at' => $recordedAt,
                    'received_at' => $recordedAt->addSeconds(rand(1, 10)),
                ]);
            }
        }

        // Update asset_latest_locations based on latest log per asset
        \Artisan::call('tracking:refresh-latest');
    }
}