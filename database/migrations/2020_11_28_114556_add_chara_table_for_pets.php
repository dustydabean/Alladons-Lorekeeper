<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCharaTableForPets extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        //
        Schema::table('user_pets', function (Blueprint $table) {
            $table->integer('chara_id')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        //
        Schema::table('user_pets', function (Blueprint $table) {
            $table->dropColumn('chara_id');
        });
    }
}
