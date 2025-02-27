<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDescriptionToLocis extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::table('locis', function (Blueprint $table) {
            // Description of the Gene Group
            $table->text('description')->nullable()->default(null);
            $table->text('parsed_description')->nullable()->default(null);

            // Determines if the Gene Group is visible in the Encyclopedia
            $table->boolean('is_visible')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::table('locis', function (Blueprint $table) {
            $table->dropColumn('description');
            $table->dropColumn('parsed_description');
            $table->dropColumn('is_visible');
        });
    }
}
