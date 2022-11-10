<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TableCreateAudiotakesIds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('audiotakes_ids', function (Blueprint $table) {
            $table->id();
            $table->string('identifier')->unique();
            $table->unsignedInteger('user_id')->index()->nullable();
            $table->string('feed_id')->index()->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('audiotakes_ids', function (Blueprint $table) {
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
        Schema::dropIfExists('audiotakes_ids');
    }
}
