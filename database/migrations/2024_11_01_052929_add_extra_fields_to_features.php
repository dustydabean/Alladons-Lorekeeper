<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('features', function (Blueprint $table) {
            $table->integer('mut_level')->nullable()->default(null);
            $table->integer('mut_type')->nullable()->default(null);
            $table->boolean('is_locked')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('features', function (Blueprint $table) {
            $table->dropColumn('mut_level');
            $table->dropColumn('mut_type');
            $table->dropColumn('is_locked');
        });
    }
};
