<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TableAudiotakesPayoutsAddField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('audiotakes_payouts', function (Blueprint $table) {
            $table->float('preshare')->after('share')->default(\App\Models\AudiotakesContract::GENERAL_SUBSTRACTION);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('audiotakes_payouts', function (Blueprint $table) {
            $table->dropColumn('preshare');
        });
    }
}
