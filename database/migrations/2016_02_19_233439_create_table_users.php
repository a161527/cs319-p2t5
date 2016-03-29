<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
          $table->increments('id');
          $table->string('firstName');
          $table->string('lastName');
          $table->date('dateOfBirth');
          $table->enum('gender', ['Male', 'Female', 'Other']);
          $table->string('location');
          $table->string('notes')->nullable();
          $table->integer('accountID')->unsigned();
          $table->boolean('approved')->default(false);
          // Many-to-One
          $table->foreign('accountID')
              ->references('id')->on('accounts')
              ->onDelete('cascade')
              ->onUpdate('cascade');
          $table->timestamps();
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
        Schema::drop('users');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
