<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RoomTablesSetup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('residences', function(Blueprint $table) {
            $table->increments('id');
            $table->string('residenceName');
            $table->string('location');
            $table->integer('conferenceID')->unsigned();
            $table->foreign('conferenceID')
                  ->references('id')->on('conferences')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });
        Schema::create('room_types', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('capacity')->unsigned();
            $table->boolean('accessible');
        });

        Schema::create('room_sets', function(Blueprint $table) {
            $table->increments('id');
            $table->string('roomName');
            $table->integer('residenceID')->unsigned();
            $table->integer('roomTypeID')->unsigned();

            $table->foreign('residenceID')
                  ->references('id')->on('residences')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            $table->foreign('roomTypeID')
                  ->references('id')->on('room_types')
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
