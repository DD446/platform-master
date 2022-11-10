<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TableCreateAudiotakesTrackingMappings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('audiotakes_tracking_mappings', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id')->index();
            $table->string('feed_id');
            $table->string('hash')->unique();
            $table->timestamps();
        });

        Schema::table('audiotakes_tracking_mappings', function (Blueprint $table) {
            $table->foreign('user_id')->references('usr_id')->on('usr');
            $table->unique(['user_id', 'feed_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('audiotakes_tracking_mappings');
    }
}
