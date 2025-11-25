<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('forages', function (Blueprint $table) {
            $table->timestamp('active_until')->nullable()->default(null);
            $table->integer('stamina_cost')->default(1);

            // currency cost stuff
            $table->boolean('has_cost')->default(false);
            $table->integer('currency_id')->nullable()->default(null);
            $table->integer('currency_quantity')->nullable()->default(null);
        });

        Schema::table('user_foraging', function (Blueprint $table) {
            // change last_forage_id column to forage_id
            $table->renameColumn('last_forage_id', 'forage_id');
            $table->renameColumn('last_foraged_at', 'foraged_at');
            // // remove distribute_at column
            // $table->dropColumn('distribute_at');

            // add character id column
            $table->integer('character_id')->nullable()->default(null);
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
        Schema::table('forages', function (Blueprint $table) {
            $table->dropColumn('active_until');
            $table->dropColumn('stamina_cost');
            $table->dropColumn('has_cost');
            $table->dropColumn('currency_id');
            $table->dropColumn('currency_quantity');
        });

        Schema::table('user_foraging', function (Blueprint $table) {
            $table->renameColumn('forage_id', 'last_forage_id');
            $table->renameColumn('foraged_at', 'last_foraged_at');
            // $table->timestamp('distribute_at')->nullable()->default(null);
            $table->dropColumn('character_id');
        });
    }
};
