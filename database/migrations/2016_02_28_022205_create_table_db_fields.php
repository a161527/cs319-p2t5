<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableDbFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('db_fields', function (Blueprint $table) {
            $table->increments('id');
            $table->string('fieldName');
            $table->integer('tableID')->unsigned();
            $table->boolean('tracked');
        });

        Schema::table('db_fields', function(Blueprint $table) {
            $table->foreign('tableID')
                  ->references('id')->on('db_tables')
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
        Schema::drop('db_fields');
    }
}
