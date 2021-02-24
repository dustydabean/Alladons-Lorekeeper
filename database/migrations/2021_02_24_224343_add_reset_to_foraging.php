<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddResetToForaging extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_foraging', function (Blueprint $table) {
            //
            $table->dropColumn('foraged');
            $table->timestamp('reset_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_foraging', function (Blueprint $table) {
            //
            $table->dropColumn('reset_at');
            $table->boolean('foraged')->default(0);
        });
    }
}
