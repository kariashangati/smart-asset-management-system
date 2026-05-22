<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('asset_latest_locations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('asset_id')
                ->constrained('assets')
                ->cascadeOnDelete();

            $table->foreignId('tracker_device_id')
                ->constrained('tracker_devices')
                ->cascadeOnDelete();

            $table->decimal('latitude', 10, 8);

            $table->decimal('longitude', 11, 8);

            $table->boolean('last_motion_detected')
                ->default(false);

            $table->timestamp('last_recorded_at')
                ->nullable();

            $table->timestamps();

            // Optional indexes for faster tracking queries
            $table->index('asset_id');
            $table->index('tracker_device_id');
            $table->index('last_recorded_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_latest_locations');
    }
};