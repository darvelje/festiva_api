<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->increments('shop_id');
            $table->integer('user_id');
            $table->timestamps();
            $table->integer('user_address_id')->nullable();
            $table->integer('shop_coupon_id')->nullable();
            $table->string('delivery_type', 50)->nullable();
            $table->integer('status_payment')->nullable();
            $table->string('currency_code', 3)->nullable();
            $table->decimal('total_price', 9)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
