<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserAccountingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_accounting', function (Blueprint $table) {
            $table->bigIncrements('accounting_id')->comment('ID');
            $table->unsignedInteger('usr_id')->comment('User ID');
            $table->unsignedSmallInteger('activity_type')->comment('Type of activity');
            $table->integer('activity_characteristic')->comment('Characteristic of activity');
            $table->string('activity_description')->comment('Descriptive text of activity');
            $table->float('amount', 10)->comment('Cost of activity');
            $table->string('currency', 5)->default('EUR')->comment('Currency');
            $table->dateTime('date_created')->comment('Creation date');
            $table->dateTime('date_start')->comment('Date activity starts');
            $table->dateTime('date_end')->comment('Date activity ends');
            $table->boolean('procedure')->comment('Automatic or manual procedure');
            $table->boolean('status')->default(0)->comment('Status of order');
        });

        if (app()->runningUnitTests()) {
            return;
        }

        Schema::table('user_accounting', function (Blueprint $table) {
            $table->unsignedInteger('usr_id')->index('usr_id')->comment('User ID')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_accounting');
    }
}
