<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableNotifications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
          $table->increments('id');
          $table->string('message');
          $table->integer('userID')->unsigned();
          $table->integer('dbtriggerID')->unsigned();
          $table->integer('changehistoryID')->unsigned();
          // Many-to-One
          $table->foreign('userID')
                ->references('id')->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
          // Many-to-One
          $table->foreign('dbtriggerID')
                ->references('id')->on('db_triggers')
                ->onDelete('cascade')
                ->onUpdate('cascade');
          // Many-to-One
          $table->foreign('changehistoryID')
              ->references('id')->on('change_history')
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
        Schema::drop('notifications');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');

    }
}
