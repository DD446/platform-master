<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserPreferenceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
/*        Schema::create('user_preference', function (Blueprint $table) {
            $table->integer('user_preference_id')->primary();
            $table->integer('usr_id')->index('usr_user_preference_fk');
            $table->integer('preference_id')->index('preference_user_preference_fk');
            $table->string('value', 128)->nullable();
        });*/
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_preference');
    }
}
