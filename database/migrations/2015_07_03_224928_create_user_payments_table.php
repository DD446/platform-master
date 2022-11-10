<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_payments_seq', function (Blueprint $table) {
            $table->bigIncrements('id');
        });

        DB::table('user_payments_seq')->insert(['id' => 0]);

        Schema::create('user_payments', function (Blueprint $table) {
            $table->integer('payment_id')->primary()->comment('Unique identifier');
            $table->unsignedInteger('receiver_id')->comment('User who receives');
            $table->unsignedInteger('payer_id')->comment('User who paid');
            $table->unsignedInteger('usr_id')->comment('User who created entry');
            $table->float('amount', 10)->comment('Amount');
            $table->string('currency', 5)->default('EUR')->comment('Currency');
            $table->unsignedTinyInteger('payment_method')->comment('Method of payment');
            $table->dateTime('date_created')->comment('Date record is added');
            $table->string('bill_id', 25)->comment('Number of bill');
            $table->boolean('is_refundable')->unsigned()->default(1)->comment('Marks if user can get back money');
            $table->text('comment')->nullable()->comment('Free text');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_payments_seq');
    }
}
