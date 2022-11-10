<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TableCreateUserUploads extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_uploads', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id')->index();
            $table->string('file_id')->index();
            $table->unsignedBigInteger('file_size');
            $table->string('file_name');
            $table->integer('space_id');
            $table->unsignedBigInteger('space_used');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('user_uploads', function (Blueprint $table) {
            $table->foreign('user_id')->references('usr_id')->on('usr');
            //$table->foreign('space_id')->references('id')->on('spaces');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_uploads');
    }
}
