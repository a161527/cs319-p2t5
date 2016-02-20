<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user', function (Blueprint $table) {
          $table->increments('id');
          $table->string('FirstName');
          $table->string('LastName');
          $table->string('DateOfBirthc');
          $table->string('Email')->unique();
          $table->string('Password');
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
        Schema::drop('user');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
