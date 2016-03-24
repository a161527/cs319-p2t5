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
            $table->string('name');
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
            $table->string('name');

            $table->integer('residenceID')->unsigned();
            $table->integer('typeID')->unsigned();

            $table->foreign('residenceID')
                  ->references('id')->on('residences')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            $table->foreign('typeID')
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
        Schema::drop('residences');
        Schema::drop('room_sets');
        Schema::drop('room_types');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
