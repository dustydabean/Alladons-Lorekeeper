<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCharacterLinks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('character_relations', function (Blueprint $table){
            $table->increments('id');
            $table->string('character_ids');
            $table->string('info')->nullable();
        });

        Schema::table('characters', function (Blueprint $table){
            $table->boolean('is_links_open')->default(1);
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
        Schema::dropIfExists('character_relations');

        Schema::table('characters', function (Blueprint $table){
            $table->dropColumn('is_links_open');
        });
    }
}
