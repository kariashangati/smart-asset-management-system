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
        Schema::table('alerts', function (Blueprint $table) {
            $table->timestamp('resolved_at')->nullable()->after('triggered_at');
            $table->text('resolution_notes')->nullable()->after('resolved_at');
            $table->index(['asset_id', 'status']);
            $table->index(['status', 'severity']);
            $table->index('triggered_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alerts', function (Blueprint $table) {
            $table->dropColumn(['resolved_at', 'resolution_notes']);
            $table->dropIndex(['asset_id', 'status']);
            $table->dropIndex(['status', 'severity']);
            $table->dropIndex(['triggered_at']);
        });
    }
};
