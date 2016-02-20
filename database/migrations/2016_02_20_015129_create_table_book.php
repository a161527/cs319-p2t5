<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableBook extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('books', function (Blueprint $table) {
          $table->increments('id');
          $table->integer('roomID')->unsigned();
          $table->integer('userID')->unsigned();
          $table->foreign('userID')
                ->references('id')->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
          $table->foreign('roomID')
                ->references('id')->on('rooms')
                ->onDelete('cascade')
                ->onUpdate('cascade');
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
        Schema::drop('books');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
