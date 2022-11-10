<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpacesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('spaces', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->bigInteger('space')->default(0);
            $table->bigInteger('space_available')->default(0);
            $table->tinyInteger('type')->default(1);
            $table->boolean('is_available')->default(true);
            $table->boolean('is_free')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('spaces', function (Blueprint $table) {
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
        Schema::dropIfExists('spaces');
    }
}
