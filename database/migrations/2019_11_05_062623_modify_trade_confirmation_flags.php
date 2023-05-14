<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyTradeConfirmationFlags extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        //
        Schema::table('trades', function (Blueprint $table) {
            $table->dropColumn('is_confirmed');
            $table->boolean('is_sender_trade_confirmed')->default(0);
            $table->boolean('is_recipient_trade_confirmed')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        //
        Schema::table('trades', function (Blueprint $table) {
            $table->dropColumn('is_sender_trade_confirmed');
            $table->dropColumn('is_recipient_trade_confirmed');
            $table->boolean('is_confirmed')->default(0);
        });
    }
}
