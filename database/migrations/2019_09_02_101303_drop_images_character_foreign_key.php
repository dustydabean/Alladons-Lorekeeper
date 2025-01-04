<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropImagesCharacterForeignKey extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        // Since we're using this table for both character images and design updates,
        // we need to get rid of this foreign key for it to work with design updates
        Schema::table('character_image_creators', function (Blueprint $table) {
            $table->dropForeign(['character_image_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        //
        Schema::table('character_image_creators', function (Blueprint $table) {
            $table->foreign('character_image_id')->references('id')->on('character_images');
        });
    }
}
