<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TableAudiotakesContractContactsCreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('audiotakes_contract_contacts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('audiotakes_contract_id')->index();
            $table->unsignedBigInteger('audiotakes_payout_contact_id')->index();
            $table->timestamps();
            $table->foreign('audiotakes_contract_id')->references('id')->on('audiotakes_contracts');
            $table->foreign('audiotakes_payout_contact_id', 'apc_id_foreign')->references('id')->on('audiotakes_payout_contacts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('audiotakes_contract_contacts');
    }
}
