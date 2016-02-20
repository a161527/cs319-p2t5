<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableSupervise extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supervise', function (Blueprint $table) {
          $table->increments('id');
          $table->integer('userID')->unsigned();
          $table->integer('conferenceID')->unsigned();
          $table->integer('eventID')->unsigned();
          $table->foreign('userID')
                ->references('id')->on('user')
                ->onDelete('cascade')
                ->onUpdate('cascade');
          $table->foreign('conferenceID')
                ->references('conferenceID')->on('event')
                ->onDelete('cascade')
                ->onUpdate('cascade');
          $table->foreign('eventID')
                ->references('id')->on('event')
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
        Schema::drop('supervise');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
