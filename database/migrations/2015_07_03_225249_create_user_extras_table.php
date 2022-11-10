<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserExtrasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_extras', function (Blueprint $table) {
            $table->bigIncrements('extras_id')->comment('ID');
            $table->unsignedInteger('usr_id')->index()->comment('User ID');
            $table->unsignedSmallInteger('extras_type')->comment('Type of extra');
            $table->integer('extras_count')->comment('Amount');
            $table->string('extras_description')->comment('Description of booked extra');
            $table->dateTime('date_created')->comment('Creation date');
            $table->dateTime('date_start')->comment('Date activity starts');
            $table->dateTime('date_end')->comment('Date activity ends');
            $table->boolean('is_repeating')->unsigned()->default(0)->comment('Marks if user booked repeating feature');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_extras');
    }
}
