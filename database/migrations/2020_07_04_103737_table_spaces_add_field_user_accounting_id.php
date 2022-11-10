<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TableSpacesAddFieldUserAccountingId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('spaces', function (Blueprint $table) {
            $table->unsignedInteger('user_id')->index()->change();
            $table->unsignedBigInteger('user_accounting_id')->after('user_id');
        });

        Schema::table('spaces', function (Blueprint $table) {
            $table->foreign('user_accounting_id')->references('accounting_id')->on('user_accounting');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('spaces', function (Blueprint $table) {
            //$table->dropColumn(['user_accounting_id']);
        });
    }
}
