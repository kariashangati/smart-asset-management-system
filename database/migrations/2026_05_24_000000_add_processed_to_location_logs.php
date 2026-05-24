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
        Schema::table('location_logs', function (Blueprint $table) {
            $table->boolean('processed')->default(false)->after('received_at');
            $table->index(['asset_id', 'recorded_at']);
            $table->index('processed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('location_logs', function (Blueprint $table) {
            $table->dropColumn('processed');
            $table->dropIndex(['asset_id', 'recorded_at']);
            $table->dropIndex('processed');
        });
    }
};
