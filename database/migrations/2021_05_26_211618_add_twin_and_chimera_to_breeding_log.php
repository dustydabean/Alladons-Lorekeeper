<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTwinAndChimeraToBreedingLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('character_breeding_log_relations', function (Blueprint $table) {
            $table->unsignedInteger('twin_id')->nullable();
            $table->foreign('twin_id')->references('id')->on('characters');
            $table->boolean('chimerism')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('character_breeding_log_relations', function (Blueprint $table) {
            $table->dropForeign('character_breeding_log_relations_twin_id_foreign');
            $table->dropColumn('twin_id');
            $table->dropColumn('chimerism');
        });
    }
}
