<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableStatsExport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stats_exports', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id')->index();
            $table->date('start')->nullable();
            $table->date('end')->nullable();
            $table->string('feed_id')->nullable();
            $table->string('show_id')->nullable();
            $table->string('sort_order')->default('desc');
            $table->string('sort_by')->default('hits');
            $table->unsignedMediumInteger('limit')->nullable();
            $table->unsignedMediumInteger('offset')->nullable();
            $table->string('restrict')->nullable();
            $table->unsignedMediumInteger('restrict_limit')->nullable();
            $table->string('filename')->nullable();
            $table->string('format')->default('csv');
            $table->unsignedMediumInteger('downloads')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('stats_exports', function (Blueprint $table) {
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
        Schema::dropIfExists('stats_exports');
    }
}
