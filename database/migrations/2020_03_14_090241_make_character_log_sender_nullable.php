<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeCharacterLogSenderNullable extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        // This is required to allow staff to transfer characters from non-registered users around
        Schema::table('user_character_log', function (Blueprint $table) {
            $table->string('sender_alias')->nullable();
            $table->integer('sender_id')->unsigned()->nullable()->change();
        });
        Schema::table('character_log', function (Blueprint $table) {
            $table->string('sender_alias')->nullable();
            $table->integer('sender_id')->unsigned()->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        //
        Schema::table('user_character_log', function (Blueprint $table) {
            $table->dropColumn('sender_alias');
            $table->integer('sender_id')->unsigned()->change();
        });
        Schema::table('character_log', function (Blueprint $table) {
            $table->dropColumn('sender_alias');
            $table->integer('sender_id')->unsigned()->change();
        });
    }
}
