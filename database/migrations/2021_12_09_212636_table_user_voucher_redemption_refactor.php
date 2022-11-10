<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TableUserVoucherRedemptionRefactor extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_voucher_redemption', function (Blueprint $table) {
            $table->increments('user_voucher_redemption_id');
            $table->unsignedInteger('usr_id');
            $table->unsignedBigInteger('voucher_id');
        });

        Schema::table('user_voucher_redemption', function (Blueprint $table) {
            $table->bigIncrements('user_voucher_redemption_id')->change();
        });

        Schema::table('user_voucher_redemption', function (Blueprint $table) {
            $table->renameColumn('user_voucher_redemption_id', 'id');
            $table->renameColumn('usr_id', 'user_id');
        });

        Schema::table('user_voucher_redemption', function (Blueprint $table) {
            $table->rename('voucher_redemptions');
        });

        Schema::table('voucher_redemptions', function (Blueprint $table) {
            $table->unsignedBigInteger('voucher_id')->change();
            $table->timestamps();
        });

        Schema::table('user_voucher_redemption_seq', function (Blueprint $table) {
            $table->dropIfExists();
        });

        $sql = 'DELETE FROM voucher_redemptions WHERE NOT EXISTS(SELECT NULL FROM usr u WHERE u.usr_id = user_id)';
        DB::connection()->getPdo()->exec($sql);

        $sql = 'DELETE FROM voucher_redemptions WHERE NOT EXISTS(SELECT NULL FROM vouchers v WHERE v.id = voucher_id)';
        DB::connection()->getPdo()->exec($sql);

        Schema::table('voucher_redemptions', function (Blueprint $table) {
            $table->foreign('voucher_id')->references('id')->on('vouchers');
            $table->foreign('user_id')->references('usr_id')->on('usr');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_voucher_redemptions', function (Blueprint $table) {
            $table->rename('voucher_redemption');
        });
    }
}
