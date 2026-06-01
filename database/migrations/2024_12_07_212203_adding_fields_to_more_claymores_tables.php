<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddingFieldsToMoreClaymoresTables extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('pets', function (Blueprint $table) {
            $table->boolean('is_visible')->default(1);
        });

        Schema::table('pet_categories', function (Blueprint $table) {
            $table->boolean('is_visible')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('pets', function (Blueprint $table) {
            $table->dropColumn('is_visible');
        });

        Schema::table('pet_categories', function (Blueprint $table) {
            $table->dropColumn('is_visible');
        });
    }
}
