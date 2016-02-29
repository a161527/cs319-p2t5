<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableRooms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rooms', function (Blueprint $table) {
          $table->increments('id');
          $table->string('residenceName');
          $table->string('roomNumber');
          $table->string('location');
          $table->integer('capacity')->unsigned();
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
        Schema::drop('rooms');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');

    }
}
