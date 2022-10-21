<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shops', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('cover', 256)->nullable();
            $table->string('avatar', 256)->nullable();
            $table->string('address', 256)->nullable();
            $table->string('phone', 256)->nullable();
            $table->string('email', 256)->nullable();
            $table->string('facebook_link', 256)->nullable();
            $table->string('instagram_link', 256)->nullable();
            $table->string('twitter_link', 256)->nullable();
            $table->string('wa_link', 256)->nullable();
            $table->string('telegram_link', 256)->nullable();
            $table->timestamps();
            $table->string('url')->nullable();
            $table->integer('user_id')->nullable();
            $table->string('slug')->nullable()->unique('shops_un');
            $table->decimal('comission', 9)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shops');
    }
}
