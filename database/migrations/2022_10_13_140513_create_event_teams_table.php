<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventTeamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_teams', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');

            $table->string('name')->nullable()->default(null);
            $table->boolean('has_image')->default(0);

            $table->integer('score')->default(0);
        });

        Schema::table('user_settings', function(Blueprint $table) {
            $table->tinyInteger('team_id')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event_teams');

        Schema::table('user_settings', function(Blueprint $table) {
            $table->dropcolumn('team_id');
        });
    }
}
