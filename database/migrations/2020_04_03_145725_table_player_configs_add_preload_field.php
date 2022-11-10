<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TablePlayerConfigsAddPreloadField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('player_configs', function (Blueprint $table) {
            $table->enum('preload', ['none', 'metadata', 'audio'])
                ->default('metadata')
                ->after('enable_shuffle')
                ->comment('Can be set to "auto" which is default and loads the entire audio, "metadata" which only preloads the metadata only, or "none" which preloads nothing.');
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
            $table->dropColumn(['preload']);
        });
    }
}
