<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToUserFavoritesHasShopProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_favorites_has_shop_products', function (Blueprint $table) {
            $table->foreign(['shop_product_id'], 'user_favorites_has_shop_products_fk0')->references(['id'])->on('shop_products')->onDelete('CASCADE');
            $table->foreign(['user_id'], 'user_favorites_has_shop_products_fk1')->references(['id'])->on('users')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_favorites_has_shop_products', function (Blueprint $table) {
            $table->dropForeign('user_favorites_has_shop_products_fk0');
            $table->dropForeign('user_favorites_has_shop_products_fk1');
        });
    }
}
