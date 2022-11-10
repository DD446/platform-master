<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeUsrIdAutoincrement extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (app()->runningUnitTests()) {
            return;
        }

        Schema::table('usr', function (Blueprint $table) {
            $sql = "SELECT id FROM usr_seq;";
            $max = DB::select($sql);
            $maxPlusOne = isset($max[0]) ? $max[0]->id + 1 : 0;

            $sql = "ALTER TABLE usr CHANGE `usr_id` `usr_id` INT UNSIGNED NOT NULL AUTO_INCREMENT, auto_increment={$maxPlusOne}";
            DB::connection()->getPdo()->exec($sql);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (app()->runningUnitTests()) {
            return;
        }

        // drop foreign key constraints
        Schema::table('user_email_queue', function (Blueprint $table) {
            $table->dropForeign('user_email_queue_user_id_foreign');
        });
        Schema::table('user_oauth', function (Blueprint $table) {
        $table->dropForeign('user_oauth_user_id_foreign');
        });
        Schema::table('voucher_redemptions', function (Blueprint $table) {
            $table->dropForeign('voucher_redemptions_user_id_foreign');
        });

        // update column
        Schema::table('usr', function (Blueprint $table) {
            $sql = "ALTER TABLE `usr` CHANGE `usr_id` `usr_id` INT(11) NOT NULL;";
            DB::connection()->getPdo()->exec($sql);
        });

        // re-insert foreign key constraints again
        Schema::table('user_email_queue', function (Blueprint $table) {
            $table->foreign('user_id')->references('usr_id')->on('usr');
        });
        Schema::table('user_oauth', function (Blueprint $table) {
            $table->foreign('user_id')->references('usr_id')->on('usr');
        });
        Schema::table('voucher_redemptions', function (Blueprint $table) {
            $table->foreign('user_id')->references('usr_id')->on('usr');
        });
    }
}
