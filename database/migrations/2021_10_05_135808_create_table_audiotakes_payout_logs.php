<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableAudiotakesPayoutLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('audiotakes_payout_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('audiotakes_payout_id');
            $table->float('funds');
            $table->float('funds_open');
            $table->float('funds_raw');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('audiotakes_payout_logs', function (Blueprint $table) {
            $table->foreign('audiotakes_payout_id')->references('id')->on('audiotakes_payouts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('audiotakes_payout_logs');
    }
}
