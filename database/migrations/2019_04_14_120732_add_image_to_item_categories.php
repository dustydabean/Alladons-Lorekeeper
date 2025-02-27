<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddImageToItemCategories extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::table('item_categories', function (Blueprint $table) {
            // Adding for the sake of symmetry...
            $table->boolean('has_image')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::table('item_categories', function (Blueprint $table) {
            //
            $table->dropColumn('has_image');
        });
    }
}
