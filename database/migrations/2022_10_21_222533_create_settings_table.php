<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('app_name')->nullable();
            $table->string('app_favicon')->nullable();
            $table->string('app_logo')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->decimal('shop_comission', 9, 0)->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->string('phone', 10)->nullable();
            $table->string('email', 50)->nullable();
            $table->string('address')->nullable();
            $table->string('description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings');
    }
}
