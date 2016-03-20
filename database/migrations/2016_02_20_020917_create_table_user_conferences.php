<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUserConferences extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_conferences', function (Blueprint $table) {
          $table->increments('id');
          $table->integer('userID')->unsigned();
          $table->integer('conferenceID')->unsigned();
          $table->integer('flightID')->unsigned()->nullable();
          $table->boolean('needsTransportation');
          $table->boolean('needsAccommodation');
          $table->boolean('approved')->default(false);
          // Many-to-One
          $table->foreign('userID')
                ->references('id')->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
          // Many-to-One
          $table->foreign('conferenceID')
                ->references('id')->on('conferences')
                ->onDelete('cascade')
                ->onUpdate('cascade');

          $table->foreign('flightID')
                ->references('id')->on('flights')
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
        Schema::drop('user_conferences');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
