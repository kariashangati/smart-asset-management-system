<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('alerts', function (Blueprint $table) {
            $table->boolean('email_sent')->default(false)->after('resolution_notes');
            $table->boolean('sms_sent')->default(false)->after('email_sent');
            $table->boolean('push_sent')->default(false)->after('sms_sent');
        });
    }

    public function down(): void
    {
        Schema::table('alerts', function (Blueprint $table) {
            $table->dropColumn(['email_sent', 'sms_sent', 'push_sent']);
        });
    }
};
