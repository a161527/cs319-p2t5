<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddConferenceidColumnToUserInventory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_inventory', function (Blueprint $table) {
            $table->integer('conferenceID')->unsigned();
            $table->foreign('conferenceID')
                ->references('id')->on('conferences')
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
        Schema::table('user_inventory', function (Blueprint $table) {
            $table->dropColumn('conferenceID');
        });
    }
}
