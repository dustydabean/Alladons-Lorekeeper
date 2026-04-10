<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPetsToSubmissionsTable extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::table('submissions', function (Blueprint $table) {
            $table->json('pets')->nullable()->default(null)->after('data');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropColumn('pets');
        });
    }
}
