<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TableNewsAddFieldsLikesAuthor extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('news', function (Blueprint $table) {
            $table->boolean('is_public')->default(true);
            $table->boolean('is_sticky')->default(false);
            $table->unsignedMediumInteger('likes')->default(0);
            $table->unsignedMediumInteger('dislikes')->default(0);
            $table->string('author')->default('Fabio');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('news', function (Blueprint $table) {
            $table->dropColumn(['is_public', 'is_sticky', 'likes', 'dislikes', 'author']);
        });
    }
}
