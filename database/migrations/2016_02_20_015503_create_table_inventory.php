<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableInventory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory', function (Blueprint $table) {
          $table->increments('id');
          $table->integer('currentQuantity');
          $table->integer('totalQuantity');
          $table->string('units')->nullable();
          $table->string('itemName');
          $table->boolean('disposable');
          $table->integer('conferenceID')->unsigned();
          // Many-to-One
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
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::drop('inventory');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
