<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableAudiotakesPayouts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('audiotakes_payouts', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id')->index();
            $table->unsignedBigInteger('audiotakes_contract_id');
            $table->float('funds')->default(0);
            $table->float('funds_open')->default(0);
            $table->float('funds_raw')->default(0);
            $table->float('holdback')->default(\App\Models\AudiotakesContract::PLATFORM_HOLDBACK);
            $table->float('share')->default(\App\Models\AudiotakesContract::DEFAULT_SHARE);
            $table->string('currency')->default('EUR');
            $table->tinyInteger('month');
            $table->mediumInteger('year');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('audiotakes_payouts', function (Blueprint $table) {
            $table->foreign('user_id')->references('usr_id')->on('usr');
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
        Schema::dropIfExists('audiotakes_payouts');
    }
}
