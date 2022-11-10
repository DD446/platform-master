<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TableAudiotakesContractsChangeShare extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('audiotakes_contracts', function (Blueprint $table) {
            $table->float('share')->default(\App\Models\AudiotakesContract::DEFAULT_SHARE)->change();
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
            //
        });
    }
}
