<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TableDpasAddFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_dpas', function (Blueprint $table) {
            $table->renameColumn('version', 'av_id');
        });

        Schema::table('user_dpas', function (Blueprint $table) {
            $table->integer('av_id')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_dpas', function (Blueprint $table) {
            $table->renameColumn('av_id', 'version');
        });
        Schema::table('user_dpas', function (Blueprint $table) {
            $table->float('version')->change();
        });
    }
}
