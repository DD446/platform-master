<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddAvNumber2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $now = \Carbon\Carbon::now();
        //$sql = "INSERT INTO `avs` (`id`, `created_at`) VALUES (2, NOW())";
        $sql = "INSERT INTO `avs` (`id`, `created_at`) VALUES (2, '{$now}')";
        DB::connection()->getPdo()->exec($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $sql = "DELETE FROM `avs` WHERE id=2";
        DB::connection()->getPdo()->exec($sql);
    }
}
