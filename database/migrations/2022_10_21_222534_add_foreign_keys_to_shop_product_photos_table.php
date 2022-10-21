<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToShopProductPhotosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shop_product_photos', function (Blueprint $table) {
            $table->foreign(['shop_product_id'], 'shop_product_photos_fk0')->references(['id'])->on('shop_products')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shop_product_photos', function (Blueprint $table) {
            $table->dropForeign('shop_product_photos_fk0');
        });
    }
}
