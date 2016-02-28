<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableAccounts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
          $table->increments('id');
          $table->string('email')->unique();
          $table->string('password');
          $table->integer('roleID')->unsigned();
          // Many-to-One
          $table->foreign('roleID')
                ->references('id')->on('roles')
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
        Schema::drop('accounts');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
