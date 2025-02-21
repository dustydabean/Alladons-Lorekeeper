<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPrereqToCollections extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::table('collections', function (Blueprint $table) {
            //
            $table->integer('parent_id')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::table('collections', function (Blueprint $table) {
            //
            $table->dropColumn('parent_id');
        });
    }
}
