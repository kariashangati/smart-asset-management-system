<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds department_id column to users table to link managers with their departments
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add department_id column after phone
            $table->unsignedBigInteger('department_id')
                ->nullable()
                ->after('phone')
                ->comment('Department assignment for asset managers. NULL for admin users.');

            // Add foreign key constraint
            $table->foreign('department_id')
                ->references('id')
                ->on('departments')
                ->onDelete('set null')
                ->comment('Cascade SET NULL on department deletion');

            // Add index for performance on queries
            $table->index('department_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['department_id']);
            
            // Drop column
            $table->dropColumn('department_id');
            
            // Drop index
            $table->dropIndex(['department_id']);
        });
    }
};
