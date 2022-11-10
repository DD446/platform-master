<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserOauthTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_oauth', function (Blueprint $table) {
            $table->integer('id', true);
            $table->unsignedInteger('user_id')->comment('User ID');
            $table->string('screen_name', 40)->comment('Twitter usename');
            $table->text('oauth_token')->nullable()->comment('OAuth access token');
            $table->string('service', 15)->comment('Name of service');
            $table->unique(['user_id', 'screen_name', 'service'], 'usr_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_oauth');
    }
}
