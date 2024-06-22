<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShopRestrictionTable extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        //
        schema::create('shop_limits', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shop_id');
            $table->integer('item_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        //
        schema::dropIfExists('shop_limits');
    }
}
