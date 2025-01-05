<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSubmissionCountToUserSettings extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::table('user_settings', function (Blueprint $table) {
            //
            $table->integer('submission_count')->unsigned()->default(0);

            // Apparently this wasn't already primary keyed
            $table->primary('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::table('user_settings', function (Blueprint $table) {
            //
            $table->dropColumn('submission_count');
        });
    }
}
