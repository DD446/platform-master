<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PodcastRouletteMatchesCreateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('podcast_roulette_matches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('roulette_id')->index();
            $table->unsignedBigInteger('roulette_partner_id')->index();
            $table->unsignedBigInteger('file_id')->nullable();
            $table->unsignedBigInteger('cover_id')->nullable();
            $table->text('shownotes')->nullable();
            $table->unsignedSmallInteger('version');
            $table->timestamps();
        });

        Schema::table('podcast_roulette_matches', function (Blueprint $table) {
            $table->foreign('roulette_id')->references('id')->on('podcast_roulettes');
            $table->foreign('roulette_partner_id')->references('id')->on('podcast_roulettes');
            $table->unique(['roulette_id', 'version']);
            $table->unique(['roulette_partner_id', 'version']);
            $table->unique(['roulette_id', 'roulette_partner_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('podcast_roulette_matches');
    }
}
