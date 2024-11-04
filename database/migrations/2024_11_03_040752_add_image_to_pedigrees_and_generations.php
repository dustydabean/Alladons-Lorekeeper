<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('character_pedigrees', function (Blueprint $table) {
            $table->boolean('has_image')->default(0);
            $table->string('hash', 10)->nullable();
        });

        Schema::table('character_generations', function (Blueprint $table) {
            $table->boolean('has_image')->default(0);
            $table->string('hash', 10)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('character_pedigrees', function (Blueprint $table) {
            $table->dropColumn('has_image');
            $table->dropColumn('hash');
        });

        Schema::table('character_generations', function (Blueprint $table) {
            $table->dropColumn('has_image');
            $table->dropColumn('hash');
        });
    }
};
