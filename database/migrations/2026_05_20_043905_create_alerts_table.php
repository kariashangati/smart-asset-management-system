<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAlertsTable extends Migration
{
    public function up()
    {
        Schema::create('alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->nullable()->constrained('assets')->nullOnDelete();
            $table->foreignId('tracker_device_id')->nullable()->constrained('tracker_devices')->nullOnDelete();
            $table->enum('alert_type', ['outside_geofence', 'device_offline', 'suspicious_motion', 'manual_notice']);
            $table->enum('severity', ['low', 'medium', 'high'])->default('medium');
            $table->string('title');
            $table->text('message');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->timestamp('triggered_at')->useCurrent();
            $table->enum('status', ['unread', 'read', 'resolved'])->default('unread');
            $table->foreignId('read_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('alerts');
    }
}