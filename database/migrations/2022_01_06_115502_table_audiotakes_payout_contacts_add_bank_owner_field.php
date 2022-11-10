<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TableAudiotakesPayoutContactsAddBankOwnerField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('audiotakes_payout_contacts', function (Blueprint $table) {
            $table->string('bank_account_owner')->after('iban')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('audiotakes_payout_contacts', function (Blueprint $table) {
            $table->dropColumn('bank_account_owner');
        });
    }
}
