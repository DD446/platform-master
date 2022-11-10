<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TableAudiotakesPayoutContactsCreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('audiotakes_payout_contacts', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id')->index();
            $table->string('name');
            $table->string('tax_id');
            $table->string('paypal')->nullable();
            $table->string('iban')->nullable();
            $table->char('country', 3);
            $table->string('vat_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('user_id')->on('usr')->references('usr_id')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('audiotakes_payout_contacts');
    }
}
