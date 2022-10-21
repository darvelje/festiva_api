<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToShopDeliveryZonesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shop_delivery_zones', function (Blueprint $table) {
            $table->foreign(['currency_code'], 'shop_delivery_zones_fk')->references(['currency_code'])->on('shop_currencies')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign(['shop_id'], 'shop_delivery_zones_fk_1')->references(['id'])->on('shops')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign(['localitie_id'], 'shop_delivery_zones_fk_2')->references(['id'])->on('localities')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign(['municipalitie_id'], 'shop_delivery_zones_fk_3')->references(['id'])->on('municipalities')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign(['province_id'], 'shop_delivery_zones_fk_4')->references(['id'])->on('provinces')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shop_delivery_zones', function (Blueprint $table) {
            $table->dropForeign('shop_delivery_zones_fk');
            $table->dropForeign('shop_delivery_zones_fk_1');
            $table->dropForeign('shop_delivery_zones_fk_2');
            $table->dropForeign('shop_delivery_zones_fk_3');
            $table->dropForeign('shop_delivery_zones_fk_4');
        });
    }
}
