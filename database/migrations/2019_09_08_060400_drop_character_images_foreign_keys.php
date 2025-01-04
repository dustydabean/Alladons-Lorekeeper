<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropCharacterImagesForeignKeys extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        // We made these columns nullable, but the foreign keys prevent them from being updated to null
        // in the case of MYO slots.
        Schema::table('character_images', function (Blueprint $table) {
            $table->dropForeign(['species_id']);
            $table->dropForeign(['rarity_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        //
        Schema::table('character_images', function (Blueprint $table) {
            $table->foreign('species_id')->references('id')->on('specieses');
            $table->foreign('rarity_id')->references('id')->on('rarities');
        });
    }
}
