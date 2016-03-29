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
        });
    }
}
