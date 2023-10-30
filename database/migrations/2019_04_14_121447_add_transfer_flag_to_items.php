<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTransferFlagToItems extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::table('items', function (Blueprint $table) {
            // Flag for whether the item can be transferred between users.
            // This flag can be overridden when the item is granted to users,
            // but otherwise will take on this value by default.
            $table->boolean('allow_transfer')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::table('items', function (Blueprint $table) {
            //
            $table->dropColumn('allow_transfer');
        });
    }
}
