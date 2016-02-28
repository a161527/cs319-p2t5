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
        Schema::create('events', function (Blueprint $table) {
          $table->increments('id');
          $table->string('eventName');
          $table->date('date');
          $table->string('location');
          $table->time('startTime');
          $table->time('endTime')->nullable();
          $table->integer('capacity');
          $table->string('description')->nullable();
          $table->integer('conferenceID')->unsigned();
          // Many-to-One
          $table->foreign('conferenceID')
                ->references('id')->on('conferences')
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
        Schema::drop('events');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
