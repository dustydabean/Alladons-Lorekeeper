<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCharacterTransferTables extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::create('character_transfers', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');

            $table->integer('character_id')->unsigned();

            $table->integer('sender_id')->unsigned();
            $table->integer('recipient_id')->unsigned();
            $table->enum('status', ['Pending', 'Accepted', 'Canceled', 'Rejected'])->default('Pending');
            $table->boolean('is_approved')->default(0);

            // Reason for the transfer being rejected by a mod
            $table->text('reason')->nullable()->default(null);

            // Information including added cooldowns
            $table->string('data')->nullable()->default(null);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::dropIfExists('character_transfers');
    }
}
