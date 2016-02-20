<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableRoom extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('room', function (Blueprint $table) {
          $table->increments('id');
          $table->string('ResidenceName');
          $table->string('Location');
          $table->string('Date');
          $table->string('RoomNumber');
          $table->integer('Capacity');
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
        Schema::drop('room');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');

    }
}
