<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TableVoucherRefactor extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('voucher', function (Blueprint $table) {
            $table->unsignedSmallInteger('voucher_action_id')->nullable()->after('voucher_id');
            $table->renameColumn('voucher_id', 'id');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('voucher', function (Blueprint $table) {
            $table->rename('vouchers');
        });

        Schema::table('voucher_seq', function (Blueprint $table) {
            $table->dropIfExists();
        });

/*        $sql = "UPDATE `vouchers` SET `created_at`=`date_created`, `voucher_action_id`= 1;";
        DB::connection()->getPdo()->exec($sql);*/
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->rename('voucher');
        });
    }
}
