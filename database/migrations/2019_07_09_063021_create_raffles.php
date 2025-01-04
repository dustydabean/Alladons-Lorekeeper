<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRaffles extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::create('raffle_groups', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name');
            $table->boolean('is_active');
        });
        Schema::create('raffles', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');

            $table->string('name');
            $table->boolean('is_active');
            $table->integer('winner_count')->unsigned()->default(1);

            $table->integer('group_id')->unsigned()->nullable()->default(null);
            $table->integer('order')->default(1);

            $table->timestamp('rolled_at')->nullable()->default(null);
        });
        Schema::create('raffle_tickets', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');

            $table->integer('user_id')->unsigned()->nullable()->default(null);
            $table->integer('raffle_id')->unsigned()->nullable()->default(null);
            $table->string('alias')->nullable()->default(null);
            $table->timestamp('created_at')->useCurrent();

            $table->integer('position')->unsigned()->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::dropIfExists('raffle_tickets');
        Schema::dropIfExists('raffles');
        Schema::dropIfExists('raffle_groups');
    }
}
