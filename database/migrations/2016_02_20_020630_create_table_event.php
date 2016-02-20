<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableEvent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event', function (Blueprint $table) {
          $table->increments('id');
          $table->string('EventName');
          $table->string('Date');
          $table->string('Location');
          $table->string('Time');
          $table->integer('SeatsCount');
          $table->integer('conferenceID')->unsigned();
          $table->foreign('conferenceID')
                ->references('id')->on('conference')
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
        Schema::drop('event');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
