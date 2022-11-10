<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUserDpas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_dpas', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();
            $table->integer('usr_id');
            $table->float('version');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('organisation')->nullable();
            $table->string('register_court')->nullable();
            $table->string('register_number')->nullable();
            $table->string('representative')->nullable();
            $table->string('addr_1')->nullable();
            $table->string('addr_2')->nullable();
            $table->string('post_code')->nullable();
            $table->string('city')->nullable();
            $table->char('country', 2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_dpas');
    }
}
