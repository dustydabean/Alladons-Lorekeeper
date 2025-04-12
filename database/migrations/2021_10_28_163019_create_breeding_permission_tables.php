<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBreedingPermissionTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('breeding_permissions', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');

            $table->integer('character_id')->unsigned()->index();
            $table->integer('recipient_id')->unsigned()->nullable()->default(null);

            $table->enum('type', ['Full', 'Split']);
            $table->boolean('is_used')->default(0);
            $table->text('description')->nullable()->default(null);

            $table->timestamps();
        });

        Schema::create('breeding_permissions_log', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');

            $table->integer('breeding_permission_id')->unsigned()->index();

            $table->integer('sender_id')->unsigned()->nullable()->default(null);
            $table->integer('recipient_id')->unsigned()->nullable()->default(null);

            $table->string('log', 255);
            $table->string('log_type', 191);
            $table->string('data', 1024)->nullable()->default(null);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('breeding_permissions');
        Schema::dropIfExists('breeding_permissions_log');
    }
}
