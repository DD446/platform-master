<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->integer('id', true);
            $table->longText('q1')->nullable();
            $table->longText('q2')->nullable();
            $table->longText('q3')->nullable();
            $table->longText('q4')->nullable();
            $table->longText('q5')->nullable();
            $table->boolean('is_public')->default(0);
            $table->integer('usr_id');
            $table->boolean('is_published')->default(0);
            $table->text('published_cite');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reviews');
    }
}
