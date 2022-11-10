<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVoucherTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('voucher', function (Blueprint $table) {
            $table->bigIncrements('voucher_id');
            $table->integer('usage_count')->default(0);
            $table->dateTime('valid_until')->nullable();
            $table->string('voucher_code');
            $table->string('comment')->nullable();
            $table->integer('valid_for')->nullable()->default(0);
            $table->integer('amount')->default(-1);
            $table->smallInteger('amount_per_person')->default(1);
            $table->dateTime('date_created')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('voucher');
    }
}
