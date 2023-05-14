<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserIdToCharacterImageCreators extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::table('character_image_creators', function (Blueprint $table) {
            //
            $table->integer('user_id')->unsigned()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::table('character_image_creators', function (Blueprint $table) {
            //
            $table->dropColumn('user_id');
        });
    }
}
