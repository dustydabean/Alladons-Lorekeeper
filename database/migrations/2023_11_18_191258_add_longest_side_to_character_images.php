<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLongestSideToCharacterImages extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('character_images', function (Blueprint $table) {
            $table->enum('longest_side', ['height', 'width'])->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('character_images', function (Blueprint $table) {
            $table->dropColumn('longest_side');
        });
    }
}
