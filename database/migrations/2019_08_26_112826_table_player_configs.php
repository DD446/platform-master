<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TablePlayerConfigs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('player_configs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->efficientUuid('uuid')->index();
            $table->unsignedInteger('user_id');
            $table->smallInteger("player_type")->default(1);
            $table->string('title');
            $table->string('default_album_art')->default('/images/podcaster_icon_weiss.svg');
            $table->integer('delay_between_audio')->default(0)->comment("Delay in milliseconds");
            $table->float('initial_playback_speed')->default(1.0);
            $table->boolean('hide_playlist_in_singlemode')->default(true);
            $table->boolean('enable_shuffle')->default(false);
            $table->boolean('debug_player')->default(false);
            $table->string('player_configurable_id');
            $table->string('player_configurable_type');
            $table->string('feed_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('player_configs', function (Blueprint $table) {
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
        Schema::dropIfExists('player_configs');
    }
}
