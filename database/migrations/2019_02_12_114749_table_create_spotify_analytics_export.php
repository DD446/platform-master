<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TableCreateSpotifyAnalyticsExport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('spotify_analytics_export', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->index();
            $table->string('show_title')->nullable();
            $table->string('data_type')->default('raw');
            $table->date('start');
            $table->date('end');
            $table->boolean('is_exported')->default(false);
            $table->unsignedSmallInteger('download_counter')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('spotify_analytics_export', function (Blueprint $table) {
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
        Schema::dropIfExists('spotify_analytics_export');
    }
}
