<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropLogForeignKeys extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        // This will drop the currency log foreign keys, so that currencies can be deleted without issue
        // even if a log exists for it
        Schema::table('currencies_log', function (Blueprint $table) {
            $table->dropForeign('banks_log_currency_id_foreign');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        // This is not actually a very accurate opposite of the above,
        // as the old index was created when the table was called banks,
        // and this will generate a different index name
        Schema::table('currencies_log', function (Blueprint $table) {
            $table->foreign('currency_id')->references('id')->on('currencies');
        });
    }
}
