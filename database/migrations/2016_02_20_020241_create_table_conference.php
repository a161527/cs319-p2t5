<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableConference extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('conferences', function (Blueprint $table) {
          $table->increments('id');
          $table->string('conferenceName');
          $table->date('dateStart');
          $table->date('dateEnd');
          $table->string('location');
          $table->string('description')->nullable();
          $table->boolean('hasTransportation');
          $table->boolean('hasAccommodations');
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
        Schema::drop('conferences');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
