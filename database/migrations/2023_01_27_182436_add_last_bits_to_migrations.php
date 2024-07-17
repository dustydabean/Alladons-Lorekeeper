<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLastBitsToMigrations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('collection_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('sort')->unsigned()->default(0);
            $table->text('description')->nullable()->default(null);
            $table->boolean('has_image')->default(0);
        });

        Schema::table('collections', function (Blueprint $table) { 
            $table->integer('collection_category_id')->unsigned()->nullable()->default(null);
            $table->boolean('is_visible')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
    }
}
