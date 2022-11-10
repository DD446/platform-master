<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TableCreateHelpVideos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('help_videos', function (Blueprint $table) {
            $table->id();
            $table->string('page_title');
            $table->text('page_description');
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->text('content');
            $table->string('username')->default('tutorials');
            $table->unsignedBigInteger('poster')->nullable();
            $table->unsignedBigInteger('mp4')->nullable();
            $table->unsignedBigInteger('webm')->nullable();
            $table->unsignedBigInteger('ogv')->nullable();
            $table->boolean('is_public')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('help_videos');
    }
}
