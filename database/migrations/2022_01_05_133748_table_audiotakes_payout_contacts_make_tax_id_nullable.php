<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TableAudiotakesPayoutContactsMakeTaxIdNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('audiotakes_payout_contacts', function (Blueprint $table) {
            $table->string('tax_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('audiotakes_payout_contacts', function (Blueprint $table) {
            //
        });
    }
}
