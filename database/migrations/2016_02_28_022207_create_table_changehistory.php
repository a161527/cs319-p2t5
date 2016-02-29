<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableChangeHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('changehistory', function (Blueprint $table) {
            $table->increments('id');
            $table->date('modificationDate');
            $table->string('oldValue')->nullable();
            $table->string('newValue')->nullable();
            $table->integer('dbfieldID')->unsigned();
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
        Schema::drop('changehistory');
    }
}
