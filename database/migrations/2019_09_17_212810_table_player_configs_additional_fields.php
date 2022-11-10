<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TablePlayerConfigsAdditionalFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('player_configs', function (Blueprint $table) {
            $table->string('icon_color')->nullable()->after('feed_id');
            $table->string('background_color')->nullable()->after('feed_id');
            $table->string('text_color')->nullable()->after('feed_id');
            $table->boolean('sharing')->default(false)->after('feed_id');
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
            $table->dropColumn(['sharing', 'text_color', 'background_color']);
        });
    }
}
