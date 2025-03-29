<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserIndexToCharacterBreedingLogs extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::table('character_breeding_logs', function (Blueprint $table) {
            $table->unsignedInteger('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::table('character_breeding_logs', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
    }
}
