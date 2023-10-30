<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCharacterSort extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        // To allow users to sort their characters
        Schema::table('characters', function (Blueprint $table) {
            $table->integer('sort')->unsigned()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        //
        Schema::table('characters', function (Blueprint $table) {
            $table->dropColumn('sort');
        });
    }
}
