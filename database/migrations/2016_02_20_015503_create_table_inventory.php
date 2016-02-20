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
          $table->string('Disposable');
          $table->string('Quantity');
          $table->string('ItemName');
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
