<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAltTypeInfoToFeatures extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('features', function (Blueprint $table) {
            //
            $table->integer('parent_id')->unsigned()->index()->nullable()->default(null);
            $table->integer('display_mode')->nullable()->default(null);
            $table->boolean('display_separate')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('features', function (Blueprint $table) {
            //
            $table->dropColumn('parent_id');
            $table->dropColumn('display_mode');
            $table->dropColumn('display_separate');
        });
    }
}
