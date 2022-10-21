<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 256)->nullable();
            $table->string('last_name', 256)->nullable();
            $table->string('email')->unique('users_email_key');
            $table->timestamp('updated_at')->nullable();
            $table->string('phone', 16)->nullable();
            $table->string('avatar', 256)->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password', 256);
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
