<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInvitationKeys extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        //
        Schema::create('invitations', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            //
            $table->increments('id');
            $table->string('code', 10)->unique();
            $table->integer('user_id')->unsigned();
            $table->integer('recipient_id')->unsigned()->nullable()->default(null);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('recipient_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        //
        Schema::dropIfExists('invitations');
    }
}
