<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableMake extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('makes', function (Blueprint $table) {
          $table->increments('id');
          $table->integer('announcementID')->unsigned();
          $table->integer('userID')->unsigned();
          $table->foreign('userID')
                ->references('id')->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
          $table->foreign('announcementID')
                ->references('id')->on('announcements')
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
        Schema::drop('makes');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
