<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class TableAudiotakesContractsMergeIds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!app()->runningUnitTests()) {
            Schema::table('audiotakes_contracts', function (Blueprint $table) {
                $table->dropForeign('audiotakes_contracts_user_id_foreign');
            });
        }
        Schema::table('audiotakes_contracts', function (Blueprint $table) {
            $table->string('identifier')->unique()->after('user_id');
            $table->string('feed_id')->index()->nullable()->after('user_id');
            $table->softDeletes();
            $table->unsignedInteger('user_id')->index()->nullable()->change();
            $table->string('first_name', 128)->nullable()->change();
            $table->string('last_name', 128)->nullable()->change();
            $table->string('street', 128)->nullable()->change();
            $table->string('housenumber', 128)->nullable()->change();
            $table->string('post_code', 16)->nullable()->change();
            $table->string('city', 64)->nullable()->change();
            $table->string('country', 2)->nullable()->change();
            $table->dateTime('audiotakes_date_accepted')->nullable()->change();
        });

        if (!app()->runningUnitTests()) {
            $sql = "UPDATE audiotakes_contracts ac INNER JOIN audiotakes_ids ai ON ac.id=ai.audiotakes_contract_id SET ac.identifier=ai.identifier, ac.feed_id=ai.feed_id";
            DB::connection()->getPdo()->exec($sql);

            $sql = "INSERT INTO `audiotakes_contracts` (`identifier`) SELECT identifier FROM `audiotakes_ids` WHERE NOT EXISTS (SELECT * FROM `audiotakes_contracts` WHERE `identifier`= audiotakes_ids.identifier LIMIT 1)";
            DB::connection()->getPdo()->exec($sql);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
/*        Schema::table('audiotakes_contracts', function (Blueprint $table) {
            $table->dropColumn('identifier');
            $table->dropColumn('feed_id');
            $table->dropSoftDeletes();
        });*/
    }
}
