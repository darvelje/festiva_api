<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopDeliveryZonesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shop_delivery_zones', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shop_id')->nullable();
            $table->integer('localitie_id')->nullable();
            $table->integer('municipalitie_id')->nullable();
            $table->integer('province_id')->nullable();
            $table->integer('time')->nullable();
            $table->string('time_type', 50)->nullable();
            $table->string('currency_code', 3)->nullable();
            $table->decimal('price', 9)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_delivery_zones');
    }
}
