<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TablePlayerConfigsIconTextColor extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('player_configs', function (Blueprint $table) {
            $table->string('progressbar_buffer_color')->nullable()->after('icon_color');
            $table->string('progressbar_color')->nullable()->after('icon_color');
            $table->string('icon_fg_color')->nullable()->after('icon_color');
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
            $table->dropColumn(['icon_fg_color','progressbar_color','progressbar_buffer_color']);
        });
    }
}
