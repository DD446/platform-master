<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TableUserPaymentsAddTimefields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_payments', function (Blueprint $table) {
            $table->timestamps();
            $table->softDeletes();
        });

        $sql = "UPDATE `user_payments` SET `created_at`=`date_created`";
        DB::connection()->getPdo()->exec($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_payments', function (Blueprint $table) {
            $table->dropTimestamps();
        });
        Schema::table('user_payments', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
}
