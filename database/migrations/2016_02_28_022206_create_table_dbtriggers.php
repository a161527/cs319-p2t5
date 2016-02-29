<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableDBTriggers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dbtriggers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->string('description');
            $table->integer('dbfieldID')->unsigned();
            $table->enum('triggerTime', ['before', 'after']);
            $table->enum('triggerEvent', ['insert', 'update', 'delete']);
            $table->string('command')->unique();
            $table->foreign('dbfieldID')
                ->references('id')->on('dbfields')
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
        Schema::drop('dbtriggers');
    }
}
