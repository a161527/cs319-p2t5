<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablePermission extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('permission', function (Blueprint $table) {
          $table->increments('id');
          $table->string('Permission');
          $table->integer('userID')->unsigned();
          $table->foreign('userID')
                ->references('userID')->on('role')
                ->onDelete('cascade')
                ->onUpdate('cascade');
          $table->integer('roleID')->unsigned();
          $table->foreign('roleID')
                ->references('id')->on('role')
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
        Schema::drop('permission');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
