<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TableAudiotakesBankTransfersCreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('audiotakes_bank_transfers', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id')->index();
            $table->float('funds');
            $table->boolean('is_paid')->default(false)->index();
            $table->timestamps();
            $table->foreign('user_id')->references('usr_id')->on('usr');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('audiotakes_bank_transfers');
    }
}
