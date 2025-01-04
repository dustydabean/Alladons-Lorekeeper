<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGiftWritingStatusToCharacters extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::table('characters', function (Blueprint $table) {
            //
            $table->boolean('is_gift_writing_allowed')->default(0);
        });

        Schema::table('character_bookmarks', function (Blueprint $table) {
            //
            $table->boolean('notify_on_gift_writing_status')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::table('characters', function (Blueprint $table) {
            //
            $table->dropColumn('is_gift_writing_allowed');
        });

        Schema::table('character_bookmarks', function (Blueprint $table) {
            //
            $table->dropColumn('notify_on_gift_writing_status');
        });
    }
}
