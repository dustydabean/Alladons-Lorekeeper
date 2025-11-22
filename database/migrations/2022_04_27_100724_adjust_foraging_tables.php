<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AdjustForagingTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('user_foraging', function (Blueprint $table) {
            //
            $table->dropColumn('reset_at');
            $table->integer('stamina')->unsigned()->default(1);
        });

        Schema::table('forages', function (Blueprint $table) {
            //
            $table->boolean('has_image')->default(0);
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
        Schema::table('user_foraging', function (Blueprint $table) {
            //
            $table->timestamp('reset_at')->nullable();
            $table->dropColumn('stamina');
        });

        Schema::table('forages', function (Blueprint $table) {
            //
            $table->dropColumn('has_image');
        });
    }
}
