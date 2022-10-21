<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToOrderProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_products', function (Blueprint $table) {
            $table->foreign(['order_id'], 'order_products_fk0')->references(['id'])->on('orders')->onDelete('CASCADE');
            $table->foreign(['shop_product_id'], 'order_products_fk1')->references(['id'])->on('shop_products')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_products', function (Blueprint $table) {
            $table->dropForeign('order_products_fk0');
            $table->dropForeign('order_products_fk1');
        });
    }
}
