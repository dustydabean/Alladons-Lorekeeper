<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserIndexToCharacterBreedingLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('character_breeding_logs', function (Blueprint $table) {
            $table->unsignedInteger("user_id");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('character_breeding_logs', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
    }
}
