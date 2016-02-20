<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableManage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('manage', function (Blueprint $table) {
        $table->increments('id');
        $table->integer('flightID')->unsigned();
        $table->integer('userID')->unsigned();
        $table->foreign('userID')
              ->references('id')->on('user')
              ->onDelete('cascade')
              ->onUpdate('cascade');
        $table->foreign('flightID')
              ->references('id')->on('flight')
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
      Schema::drop('manage');
      DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
