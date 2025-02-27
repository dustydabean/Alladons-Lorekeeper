<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDefaultValuesToLocisTable extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::table('locis', function (Blueprint $table) {
            $table->unsignedBigInteger('default')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::table('locis', function (Blueprint $table) {
            $table->dropColumn('default');
        });
    }
}
