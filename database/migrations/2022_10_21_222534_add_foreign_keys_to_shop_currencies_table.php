<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToShopCurrenciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shop_currencies', function (Blueprint $table) {
            $table->foreign(['currency_code'], 'shop_currencies_fk')->references(['code'])->on('currencies')->onDelete('CASCADE');
            $table->foreign(['shop_id'], 'shop_currencies_fk_1')->references(['id'])->on('shops')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shop_currencies', function (Blueprint $table) {
            $table->dropForeign('shop_currencies_fk');
            $table->dropForeign('shop_currencies_fk_1');
        });
    }
}
