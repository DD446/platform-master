<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePodcastRoulettesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('podcast_roulettes', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id')->index();
            $table->string('feed_id');
            $table->string('email');
            $table->text('podcasters');
            $table->unsignedSmallInteger('version');
            $table->boolean('first_time')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('podcast_roulettes', function (Blueprint $table) {
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
        Schema::dropIfExists('podcast_roulettes');
    }
}
