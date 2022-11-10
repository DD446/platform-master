<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserSessionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
/*        Schema::create('user_session', function (Blueprint $table) {
            $table->string('session_id')->primary();
            $table->dateTime('last_updated')->nullable()->index('last_updated');
            $table->text('data_value')->nullable();
            $table->integer('usr_id')->default(0)->index();
            $table->string('username', 64)->nullable()->index('username');
            $table->integer('expiry')->default(0);
        });*/
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_session');
    }
}
