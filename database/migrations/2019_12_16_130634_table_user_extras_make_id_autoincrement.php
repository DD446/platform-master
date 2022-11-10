<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TableUserExtrasMakeIdAutoincrement extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_extras', function (Blueprint $table) {
/*            $sql = "SELECT id FROM user_extras_seq;";
            $max = DB::select($sql);
            $maxPlusOne = $max[0]->id + 1;

            $sql = "ALTER TABLE user_extras CHANGE `extras_id` `extras_id` INT(11) NOT NULL AUTO_INCREMENT, auto_increment={$maxPlusOne}";
            DB::connection()->getPdo()->exec($sql);*/
            $table->bigIncrements('extras_id')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_extras', function (Blueprint $table) {
/*            $sql = "ALTER TABLE `user_extras` CHANGE `extras_id` `extras_id` INT(11) NOT NULL;";
            DB::connection()->getPdo()->exec($sql);*/
            $table->integer('extras_id')->change();
        });
    }
}
