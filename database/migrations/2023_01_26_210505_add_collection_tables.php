<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCollectionTables extends Migration
{
        /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('collections', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name', 191)->nullable(false);
            $table->boolean('has_image')->default(false)->nullable(false);
            $table->text('description')->nullable();
            $table->text('parsed_description')->nullable();
            $table->string('reference_url', 200)->nullable();
            $table->string('artist_alias', 191)->nullable();
            $table->string('artist_url', 191)->nullable();
            $table->text('output')->nullable()->default(null);
        });


        Schema::create('collection_ingredients', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->unsignedInteger('collection_id')->nullable(false);
            $table->enum('ingredient_type', ['Item'])->nullable(false);
            $table->string('ingredient_data', 1024)->nullable(false);
            $table->unsignedInteger('quantity')->nullable(false);
        });

        Schema::create('collection_rewards', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->unsignedInteger('collection_id')->nullable(false);
            $table->string('rewardable_type', 32)->nullable(false);
            $table->unsignedInteger('rewardable_id')->nullable(false);
            $table->unsignedInteger('quantity')->nullable(false);
        });

        Schema::create('user_collections', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('collection_id'); //
            $table->timestamps();
        });

        Schema::create('user_collections_log', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');

            $table->unsignedInteger('collection_id'); // The ID of the collection from the collections table

            $table->string('log', 500); // Actual log text
            $table->string('log_type'); // Indicates how the collection was received.
            $table->string('data', 1024)->nullable(); // Includes information like staff notes, etc.

            // The sender_id, if granted by an admin, is the admin user's id.
            // Recipes shouldn't be user-user transferrable, so if it's null then it's implied that it's purchased.
            // Recipes should always belong to a user, but they can be purchased using character currency, ergo character_id
            $table->unsignedInteger('sender_id')->nullable(); 
            $table->unsignedInteger('recipient_id')->nullable(); // Nullable in the case that a collection has to be rescinded for whatever reason
            $table->unsignedInteger('character_id')->nullable(); 

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
        Schema::dropIfExists('collections');
        Schema::dropIfExists('collection_ingredient');
        Schema::dropIfExists('collection_rewards');
        Schema::dropIfExists('user_collections_log');
        Schema::dropIfExists('user_collections');
    }
}
