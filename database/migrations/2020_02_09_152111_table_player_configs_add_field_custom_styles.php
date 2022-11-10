<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TablePlayerConfigsAddFieldCustomStyles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('player_configs', function (Blueprint $table) {
            $table->longText('custom_styles')->nullable()->after('progressbar_buffer_color');
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
            $table->dropColumn(['custom_styles']);
        });
    }
}
