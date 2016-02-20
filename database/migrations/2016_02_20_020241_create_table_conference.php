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
        Schema::create('conference', function (Blueprint $table) {
          $table->increments('id');
          $table->string('conferenceName');
          $table->string('dateStart');
          $table->string('dateEnd');
          $table->string('location');
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
        Schema::drop('conference');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
