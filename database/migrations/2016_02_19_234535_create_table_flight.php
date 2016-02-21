<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableFlight extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('flights', function (Blueprint $table) {
          $table->increments('id');
          $table->string('flightNumber');
          $table->string('flightDate');
          $table->string('airlines');
          $table->string('arrivalTime');
          $table->integer('passengerCount')->unsigned();
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
        Schema::drop('flights');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
