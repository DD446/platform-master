<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TableAudiotakesContractPartnersCreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('audiotakes_contract_partners', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id')->index();
            $table->string('first_name', 128)->nullable();
            $table->string('last_name', 128)->nullable();
            $table->string('organisation')->nullable()->comment('Organisation of user');
            $table->string('street', 128)->nullable();
            $table->string('housenumber', 128)->nullable();
            $table->string('post_code', 16)->nullable();
            $table->string('city', 64)->nullable();
            $table->string('country', 2)->nullable();
            $table->string('email', 32)->nullable();
            $table->string('telephone', 16)->nullable();
            $table->string('vat_id', 15)->nullable()->comment('VAT ID');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('user_id')->references('usr_id')->on('usr')->cascadeOnDelete();
        });

        Schema::table('audiotakes_contracts', function (Blueprint $table) {
            $table->unsignedBigInteger('audiotakes_contract_partner_id')->after('user_id')->nullable();
            $table->foreign('audiotakes_contract_partner_id', 'acp2_id_foreign')->references('id')->on('audiotakes_contract_partners');
        });

        $ac = \App\Models\AudiotakesContract::withTrashed()->get();

        foreach($ac as $contract) {
            if ($contract->user_id) {
                $acp = \App\Models\AudiotakesContractPartner::create(
                    [
                        'user_id' => $contract->user_id,
                        'first_name' => $contract->first_name,
                        'last_name' => $contract->last_name,
                        'organisation' => $contract->organisation,
                        'street' => $contract->street,
                        'housenumber' => $contract->housenumber,
                        'post_code' => $contract->post_code,
                        'city' => $contract->city,
                        'country' => $contract->country,
                        'telephone' => $contract->telephone,
                        'vat_id' => $contract->vat_id,
                    ]
                );

                if ($acp) {
                    $contract->update(['audiotakes_contract_partner_id' => $acp->id]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('audiotakes_contracts', function (Blueprint $table) {
            $table->dropForeign('acp2_id_foreign');
        });

        Schema::dropIfExists('audiotakes_contract_partners');

        Schema::table('audiotakes_contracts', function (Blueprint $table) {
            $table->dropColumn('audiotakes_contract_partner_id');
        });
    }
}
