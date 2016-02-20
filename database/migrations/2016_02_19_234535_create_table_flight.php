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
        Schema::create('flight', function (Blueprint $table) {
          $table->increments('id');
          $table->string('FlightNumber');
          $table->string('FlightDate');
          $table->string('Airlines');
          $table->string('ArrivalTime');
          $table->integer('PassengerCount')->unsigned();
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
        Schema::drop('flight');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
