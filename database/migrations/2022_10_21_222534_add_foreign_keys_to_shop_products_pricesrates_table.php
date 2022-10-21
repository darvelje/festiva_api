<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToShopProductsPricesratesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shop_products_pricesrates', function (Blueprint $table) {
            $table->foreign(['currency_code'], 'shop_products_pricesrates_fk')->references(['currency_code'])->on('shop_currencies')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign(['shop_product_id'], 'shop_products_pricesrates_fk1')->references(['id'])->on('shop_products')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shop_products_pricesrates', function (Blueprint $table) {
            $table->dropForeign('shop_products_pricesrates_fk');
            $table->dropForeign('shop_products_pricesrates_fk1');
        });
    }
}
