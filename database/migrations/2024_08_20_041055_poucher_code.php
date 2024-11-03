<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PoucherCode extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::table('characters', function (Blueprint $table) {
            $table->string('poucher_code', 191)->nullable()->after('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::table('characters', function (Blueprint $table) {
            $table->dropColumn('poucher_code');
        });
    }
}
