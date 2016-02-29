<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class EntrustPivotAccountRole extends Migration
{
    /**
     * Run the migrations.
     *
     * @return  void
     */
    public function up()
    {
        // Create table for associating roles to users (Many-to-Many)
        Schema::create('account_role', function (Blueprint $table) {
                        $table->integer('account_id')->unsigned();
            $table->foreign('account_id')->references('id')->on('accounts')
            ->onUpdate('cascade')->onDelete('cascade');
                        $table->integer('role_id')->unsigned();
            $table->foreign('role_id')->references('id')->on('roles')
            ->onUpdate('cascade')->onDelete('cascade');
                        $table->primary(['account_id', 'role_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return  void
     */
    public function down()
    {
        Schema::dropIfExists('account_role');
    }
}