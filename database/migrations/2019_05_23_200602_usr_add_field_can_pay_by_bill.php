<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UsrAddFieldCanPayByBill extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('usr', function (Blueprint $table) {
            $table->boolean('can_pay_by_bill')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('usr', function (Blueprint $table) {
            $table->dropColumn('can_pay_by_bill');
        });
    }
}
