<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFriendTables extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        //
        Schema::create('user_friends', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('initiator_id');
            $table->unsignedBigInteger('recipient_id');
            $table->boolean('recipient_approved')->default(0); // if the user denies just delete the record
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('approved_at')->nullable()->default(null);
        });

        Schema::create('user_blocks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('blocked_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('user_friends');
        Schema::dropIfExists('user_blocks');
    }
}
