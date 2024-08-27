<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShopPurchaseLimits extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        //
        Schema::table('shop_stock', function (Blueprint $table) {
            $table->integer('purchase_limit')->unsigned()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        //
        Schema::table('shop_stock', function (Blueprint $table) {
            $table->dropColumn('purchase_limit');
        });
    }
}
