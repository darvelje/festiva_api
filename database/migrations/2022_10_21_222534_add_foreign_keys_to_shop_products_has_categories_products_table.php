<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToShopProductsHasCategoriesProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shop_products_has_categories_products', function (Blueprint $table) {
            $table->foreign(['category_product_id'], 'shop_products_has_categories_products_fk')->references(['id'])->on('categories_products')->onDelete('CASCADE');
            $table->foreign(['shop_product_id'], 'shop_products_has_categories_products_fk1')->references(['id'])->on('shop_products')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shop_products_has_categories_products', function (Blueprint $table) {
            $table->dropForeign('shop_products_has_categories_products_fk');
            $table->dropForeign('shop_products_has_categories_products_fk1');
        });
    }
}
