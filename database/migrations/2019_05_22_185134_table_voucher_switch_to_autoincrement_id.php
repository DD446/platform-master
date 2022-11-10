<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TableVoucherSwitchToAutoincrementId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (app()->runningUnitTests()) {
            return;
        }
        Schema::table('voucher', function (Blueprint $table) {
            $table->bigIncrements('voucher_id')->change();
            $table->dateTime('valid_until')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (app()->runningUnitTests()) {
            return;
        }
        Schema::table('voucher', function (Blueprint $table) {
            //$table->integer('voucher_id')->change();
            //$table->dateTime('valid_until')->change();
        });
    }
}
