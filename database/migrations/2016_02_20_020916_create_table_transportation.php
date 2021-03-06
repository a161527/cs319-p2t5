<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableTransportation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transportation', function (Blueprint $table) {
          $table->increments('id');
          $table->integer('capacity')->unsigned();
          $table->integer('conferenceID')->unsigned();
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
      Schema::drop('transportation');
      DB::statement('SET FOREIGN_KEY_CHECKS = 1');
       }
}
