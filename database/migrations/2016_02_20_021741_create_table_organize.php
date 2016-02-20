<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableOrganize extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('organize', function (Blueprint $table) {
          $table->increments('id');
          $table->integer('userID')->unsigned();
          $table->integer('conferenceID')->unsigned();
          $table->foreign('userID')
                ->references('id')->on('user')
                ->onDelete('cascade')
                ->onUpdate('cascade');
          $table->foreign('conferenceID')
                ->references('id')->on('conference')
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
        Schema::drop('organize');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
