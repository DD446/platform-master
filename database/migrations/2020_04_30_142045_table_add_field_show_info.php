<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TableAddFieldShowInfo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('player_configs', function (Blueprint $table) {
            $table->boolean('show_info')->default(false)->after('show_playlist');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('player_configs', function (Blueprint $table) {
            $table->dropColumn(['show_info']);
        });
    }
}
