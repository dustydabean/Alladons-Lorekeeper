<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixInventoryTablesAgain extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        //
        Schema::table('user_items_log', function (Blueprint $table) {
            $table->dropForeign('inventory_log_stack_id_foreign');
            $table->dropColumn('stack_id');
        });
        Schema::table('user_items_log', function (Blueprint $table) {
            $table->integer('stack_id')->unsigned()->nullable();
            $table->foreign('stack_id')->references('id')->on('user_items');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        //
        Schema::table('user_items_log', function (Blueprint $table) {
            $table->dropForeign('inventory_log_stack_id_foreign');
            $table->dropColumn('stack_id');
        });
        Schema::table('user_items_log', function (Blueprint $table) {
            $table->integer('stack_id')->unsigned();
            $table->foreign('stack_id')->references('id')->on('user_items');
        });
    }
}
