<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableAccount extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account', function (Blueprint $table) {
          $table->increments('id');
          $table->string('email')->unique();
          $table->string('password');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::drop('account');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
