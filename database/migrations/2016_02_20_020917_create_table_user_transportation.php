<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUserTransportation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_transportation', function (Blueprint $table) {
          $table->increments('id');
          $table->integer('userconferenceID')->unsigned()->unique();
          // $table->integer('flightID')->unsigned();
          $table->integer('transportationID')->unsigned();
          // One-to-One
          $table->foreign('userconferenceID')
                ->references('id')->on('user_conferences')
                ->onDelete('cascade')
                ->onUpdate('cascade');
          // Many-to-One
          // $table->foreign('flightID')
          //       ->references('id')->on('flights')
          //       ->onDelete('cascade')
          //       ->onUpdate('cascade');
          // Many-to-One
          $table->foreign('transportationID')
                ->references('id')->on('transportation')
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
        Schema::drop('user_transportation');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
