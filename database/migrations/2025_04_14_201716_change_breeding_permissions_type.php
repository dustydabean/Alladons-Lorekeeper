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
        Schema::table('breeding_permissions', function (Blueprint $table) {
            DB::statement('ALTER TABLE breeding_permissions modify type enum( "Full Fur", "Normal" )');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('breeding_permissions', function (Blueprint $table) {
            DB::statement('ALTER TABLE breeding_permissions modify type enum( "Full", "Split" )');
        });
    }
};
