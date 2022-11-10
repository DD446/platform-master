<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TableSpotifyAnalytics extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('spotify_analytics', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->index();
            $table->string('feed_id')->index();
            $table->string('show_title');
            $table->string('file');
            $table->date('date');
            $table->string('version');
            $table->json('data');
            //$table->unique(['user_id', 'feed_id', 'file', 'date']);
        });

        Schema::table('spotify_analytics', function (Blueprint $table) {
            $table->foreign('user_id')->references('usr_id')->on('usr');
        });

        if (app()->runningUnitTests()) {
            return;
        }
        DB::statement('ALTER TABLE `spotify_analytics` CHANGE `show_title` `show_title` VARCHAR (255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('spotify_analytics');
    }
}
