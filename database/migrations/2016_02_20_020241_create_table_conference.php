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
          $table->string('ConferenceName');
          $table->string('Date Start');
          $table->string('Date End');
          $table->string('Location');
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
