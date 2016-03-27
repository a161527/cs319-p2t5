<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInfoColumnsToTransportationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transportation', function (Blueprint $table) {
            $table->string('name');
            $table->string('company')->nullable();
            $table->string('phone');
            $table->integer('conferenceID')->unsigned()->nullable();
            $table->integer('flightID')->unsigned()->nullable();
            $table->foreign('conferenceID')
                ->references('id')->on('conferences')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('flightID')
                ->references('id')->on('flights')
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
        Schema::table('transportation', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->dropColumn('company');
            $table->dropColumn('phone');
            $table->dropForeign('conferenceID');
            $table->dropForeign('flightID');
            $table->dropColumn('conferenceID');
            $table->dropColumn('flightID');
        });
    }
}
