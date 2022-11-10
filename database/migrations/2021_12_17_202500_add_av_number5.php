<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddAvNumber5 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('avs', function (Blueprint $table) {
            $now = \Carbon\Carbon::now();
            $sql = "INSERT INTO `avs` (`id`, `created_at`) VALUES (5, '{$now}')";
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
        Schema::table('avs', function (Blueprint $table) {
            $sql = "DELETE FROM `avs` WHERE id=5";
            DB::connection()->getPdo()->exec($sql);
        });
    }
}
