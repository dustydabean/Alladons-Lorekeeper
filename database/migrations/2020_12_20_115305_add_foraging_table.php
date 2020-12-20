<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForagingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('user_foraging', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('last_forage_id')->unsigned()->nullable();
            $table->timestamp('last_foraged_at')->nullable();
            $table->timestamp('distribute_at')->nullable();
            $table->boolean('foraged')->default(0);
        });

        Schema::create('forages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('display_name');
            $table->boolean('is_active')->default(0);
        });

        Schema::create('forage_rewards', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->integer('forage_id')->unsigned();
            $table->string('rewardable_type');
            $table->integer('rewardable_id')->unsigned();
            
            $table->integer('quantity')->unsigned();
            $table->integer('weight')->unsigned();
            
            $table->foreign('forage_id')->references('id')->on('forages');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::dropIfExists('user_foraging');
        Schema::dropIfExists('forage_rewards');
        Schema::dropIfExists('forages');
    }
}
