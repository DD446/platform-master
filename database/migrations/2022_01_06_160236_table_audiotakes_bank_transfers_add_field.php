<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TableAudiotakesBankTransfersAddField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('audiotakes_contract_contacts');

        Schema::table('audiotakes_bank_transfers', function (Blueprint $table) {
            $table->unsignedBigInteger('audiotakes_payout_contact_id')->after('user_id')->index();
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
        Schema::table('audiotakes_bank_transfers', function (Blueprint $table) {
            $table->dropForeign('apc_id_foreign');
            $table->dropColumn('audiotakes_payout_contact_id');
        });
    }
}
