<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TableAudiotakesBankTranfersAddContractField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('audiotakes_bank_transfers', function (Blueprint $table) {
            $table->unsignedBigInteger('audiotakes_contract_partner_id')->nullable()->after('audiotakes_payout_contact_id');
        });

        Schema::table('audiotakes_bank_transfers', function (Blueprint $table) {
            $table->foreign('audiotakes_contract_partner_id', 'acp_id_foreign')->references('id')->on('audiotakes_contract_partners');
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
            $table->dropForeign('acp_id_foreign');
            $table->dropColumn('audiotakes_contract_partner_id');
        });
    }
}
