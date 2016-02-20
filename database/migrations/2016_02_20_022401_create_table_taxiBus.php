<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableTaxiBus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('taxiBus', function (Blueprint $table) {
          $table->increments('id');
          $table->string('TaxiBusNumber');
          $table->integer('Capacity');
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
      Schema::drop('taxiBus');
      DB::statement('SET FOREIGN_KEY_CHECKS = 1');
       }
}
