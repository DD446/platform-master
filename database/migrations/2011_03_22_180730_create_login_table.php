<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoginTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
/*        Schema::create('login', function (Blueprint $table) {
            $table->integer('login_id')->primary();
            $table->integer('usr_id')->nullable()->index('usr_login_fk');
            $table->dateTime('date_time')->nullable();
            $table->string('remote_ip', 16)->nullable();
        });*/
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('login');
    }
}
