<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocationLogsTable extends Migration
{
    public function up()
    {
        Schema::create('location_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tracker_device_id')->constrained('tracker_devices')->cascadeOnDelete();
            $table->foreignId('asset_id')->nullable()->constrained('assets')->nullOnDelete();
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->decimal('speed', 5, 2)->nullable();
            $table->boolean('motion_detected')->default(false);
            $table->timestamp('recorded_at')->nullable();
            $table->timestamp('received_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('location_logs');
    }
}