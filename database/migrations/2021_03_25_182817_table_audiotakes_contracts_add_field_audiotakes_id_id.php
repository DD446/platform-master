<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TableAudiotakesContractsAddFieldAudiotakesIdId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('audiotakes_ids', function (Blueprint $table) {
            $table->unsignedBigInteger('audiotakes_contract_id')->after('feed_id')->nullable();
        });

        Schema::table('audiotakes_ids', function (Blueprint $table) {
            $table->foreign('audiotakes_contract_id')->references('id')->on('audiotakes_contracts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('audiotakes_contracts', function (Blueprint $table) {
            //$table->dropColumn('audiotakes_id_id');
        });
    }
}
