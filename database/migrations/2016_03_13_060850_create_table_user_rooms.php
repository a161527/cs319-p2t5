<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUserRooms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_rooms', function (Blueprint $table) {
          $table->increments('id');
          $table->integer('roomSetID')->unsigned();
          $table->string('roomName');
          $table->integer('registrationID')->unsigned();
          // Many-to-One
          $table->foreign('registrationID')
                ->references('id')->on('user_conferences')
                ->onDelete('cascade')
                ->onUpdate('cascade');
          // Many-to-One
          $table->foreign('roomSetID')
                ->references('id')->on('room_sets')
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
        Schema::drop('user_rooms');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
