<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrackerDevicesTable extends Migration
{
    public function up()
    {
        Schema::create('tracker_devices', function (Blueprint $table) {
            $table->id();
            $table->string('device_code')->unique();
            $table->string('device_name');
            $table->string('imei')->unique()->nullable();
            $table->string('sim_number')->nullable();
            $table->string('api_token_hash')->unique();
            $table->enum('status', ['active', 'inactive', 'faulty'])->default('active');
            $table->timestamp('last_seen_at')->nullable();
            $table->integer('battery_level')->nullable();
            $table->string('firmware_version')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tracker_devices');
    }
}