<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TableCreateSupporters extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supporters', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('supporter_id');
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['user_id', 'supporter_id']);
        });

        Schema::table('supporters', function (Blueprint $table) {
            $table->foreign('user_id')->references('usr_id')->on('usr');
            $table->foreign('supporter_id')->references('usr_id')->on('usr');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('supporters');
    }
}
