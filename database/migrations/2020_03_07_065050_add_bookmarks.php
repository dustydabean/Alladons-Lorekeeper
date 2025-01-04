<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBookmarks extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        //
        Schema::create('character_bookmarks', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');

            $table->integer('user_id')->unsigned()->index();
            $table->integer('character_id')->unsigned()->index();
            $table->boolean('notify_on_trade_status')->default(0);
            $table->boolean('notify_on_gift_art_status')->default(0);
            $table->boolean('notify_on_transfer')->default(0);
            $table->boolean('notify_on_image')->default(0);
            $table->string('comment', 512)->nullable()->default(null);

            $table->unique(['user_id', 'character_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        //
        Schema::dropIfExists('character_bookmarks');
    }
}
