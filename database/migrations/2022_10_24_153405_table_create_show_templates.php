<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TableCreateShowTemplates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('show_templates', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id')->index();
//            $table->foreignId('user_id')->constrained('usr', 'usr_id');
            $table->string('name', 120);
            $table->string('feed_id')->nullable();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('author')->nullable();
            $table->string('copyright')->nullable();
            $table->string('link')->nullable();
            $table->string('itunes_title')->nullable();
            $table->string('itunes_subtitle')->nullable();
            $table->text('itunes_summary')->nullable();
            $table->enum('itunes_episode_type', ['full', 'bonus', 'trailer'])->default('full');
            $table->integer('itunes_season')->nullable();
            $table->boolean('itunes_explicit')->default(false);
            $table->integer('itunes_logo')->nullable();
            $table->integer('is_public')->default(\App\Models\Show::PUBLISH_DRAFT);
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['user_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('show_templates');
    }
}
