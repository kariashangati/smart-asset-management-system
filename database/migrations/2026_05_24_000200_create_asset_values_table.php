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
        Schema::create('asset_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->onDelete('cascade');
            $table->decimal('purchase_price', 12, 2)->nullable();
            $table->decimal('current_value', 12, 2)->nullable();
            $table->float('depreciation_rate')->default(0);
            $table->timestamp('last_valued_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index('asset_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_values');
    }
};
