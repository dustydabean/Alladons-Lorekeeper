<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSortToLocisTable extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::table('locis', function (Blueprint $table) {
            $table->unsignedInteger('sort')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::table('locis', function (Blueprint $table) {
            $table->dropColumn('sort');
        });
    }
}
