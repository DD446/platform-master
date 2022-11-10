<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFaqTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('faq', function (Blueprint $table) {
            $table->bigIncrements('faq_id');
            $table->dateTime('date_created')->nullable()->useCurrent();
            $table->dateTime('last_updated')->nullable()->useCurrent();
            $table->string('question')->nullable();
            $table->text('answer')->nullable();
            $table->integer('item_order')->nullable();
            $table->smallInteger('is_hidden')->nullable();
            $table->integer('category_id')->nullable()->index('category_id');
            $table->unsignedMediumInteger('likes')->default(0);
            $table->unsignedMediumInteger('dislikes')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('faq');
    }
}
