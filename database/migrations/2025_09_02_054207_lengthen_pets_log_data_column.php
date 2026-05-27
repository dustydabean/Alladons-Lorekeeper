<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('user_pets_log', function (Blueprint $table) {
            $table->text('data')->nullable()->change();
        });
        Schema::table('user_pet_levels', function (Blueprint $table) {
            $table->timestamp('next_level_at')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('user_pets_log', function (Blueprint $table) {
            $table->string('data', 1024)->nullable()->change();
        });
        Schema::table('user_pet_levels', function (Blueprint $table) {
            $table->dropColumn('next_level_at');
        });
    }
};
