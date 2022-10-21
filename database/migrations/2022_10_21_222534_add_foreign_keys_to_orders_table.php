<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreign(['user_address_id'], 'orders_fk')->references(['id'])->on('user_addresses')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign(['shop_id'], 'orders_fk0')->references(['id'])->on('shops')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign(['user_id'], 'orders_fk1')->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign(['currency_code'], 'orders_fk2')->references(['currency_code'])->on('shop_currencies')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign(['shop_coupon_id'], 'orders_fk_1')->references(['id'])->on('shop_coupons')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign('orders_fk');
            $table->dropForeign('orders_fk0');
            $table->dropForeign('orders_fk1');
            $table->dropForeign('orders_fk2');
            $table->dropForeign('orders_fk_1');
        });
    }
}
