<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSubmittedAtToDesignUpdates extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::table('design_updates', function (Blueprint $table) {
            //
            $table->timestamp('submitted_at')->nullable()->default(null)->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::table('design_updates', function (Blueprint $table) {
            //
            $table->dropColumn('submitted_at');
        });
    }
}
