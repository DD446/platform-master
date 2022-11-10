<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserCookieTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
/*        Schema::create('user_cookie', function (Blueprint $table) {
            $table->unsignedInteger('usr_id')->index();
            $table->string('cookie_name', 32)->index('cookie_name');
            $table->dateTime('login_time');
        });*/
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_cookie');
    }
}
