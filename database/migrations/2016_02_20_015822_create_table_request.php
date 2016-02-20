<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableRequest extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request', function (Blueprint $table) {
          $table->increments('id');
          $table->integer('roomID')->unsigned();
          $table->integer('itemID')->unsigned();
          $table->foreign('itemID')
                ->references('id')->on('inventory')
                ->onDelete('cascade')
                ->onUpdate('cascade');
          $table->foreign('roomID')
                ->references('id')->on('room')
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
        Schema::drop('request');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
