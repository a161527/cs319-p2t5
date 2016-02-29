<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableDBFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dbfields', function (Blueprint $table) {
            $table->increments('id');
            $table->string('fieldName');
            $table->integer('tableID')->unsigned();
            $table->boolean('tracked');
        });

        Schema::table('dbfields', function(Blueprint $table) {
            $table->foreign('tableID')
                  ->references('id')->on('dbtables')
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
        Schema::drop('dbfields');
    }
}
