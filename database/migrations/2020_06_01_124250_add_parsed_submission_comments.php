<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddParsedSubmissionComments extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::table('submissions', function (Blueprint $table) {
            //
            $table->text('parsed_staff_comments')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::table('submissions', function (Blueprint $table) {
            //
            $table->dropColumn('parsed_staff_comments');
        });
    }
}
