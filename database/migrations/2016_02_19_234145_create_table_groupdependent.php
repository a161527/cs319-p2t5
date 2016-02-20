<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableGroupdependent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('groupdependent', function (Blueprint $table) {
          $table->increments('id');
          $table->string('dateOfBirth');
          $table->string('firstName');
          $table->string('lastName');
          $table->integer('userID')->unsigned();
          $table->foreign('userID')
                ->references('id')->on('user')
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
        Schema::drop('groupdependent');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
