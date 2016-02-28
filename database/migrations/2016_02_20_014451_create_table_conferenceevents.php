<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableConferenceEvents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('conferenceEvents', function (Blueprint $table) {
          $table->increments('id');
          $table->integer('conferenceID')->unsigned();
          $table->integer('eventID')->unsigned()->unique();
          // Many-to-One
          $table->foreign('conferenceID')
                ->references('id')->on('conferences')
                ->onDelete('cascade')
                ->onUpdate('cascade');
          // One-to-One
          $table->foreign('eventID')
                ->references('id')->on('events')
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
        Schema::drop('conferenceevents');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
