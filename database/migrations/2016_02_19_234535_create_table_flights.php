<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableFlights extends Migration
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
          $table->string('airline');
          $table->date('arrivalDate');
          $table->time('arrivalTime');
          $table->string('airport');
          $table->boolean('isChecked');
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
