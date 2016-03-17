<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableDbTriggers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('db_triggers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->string('description');
            $table->integer('dbfieldID')->unsigned();
            $table->enum('triggerTime', ['before', 'after']);
            $table->enum('triggerEvent', ['insert', 'update', 'delete']);
            $table->string('command')->unique();
            $table->foreign('dbfieldID')
                ->references('id')->on('db_fields')
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
        Schema::drop('db_triggers');
    }
}
