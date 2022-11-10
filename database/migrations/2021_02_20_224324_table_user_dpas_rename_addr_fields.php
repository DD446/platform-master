<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TableUserDpasRenameAddrFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_dpas', function (Blueprint $table) {
            $table->renameColumn('addr_1', 'street');
        });
        Schema::table('user_dpas', function (Blueprint $table) {
            $table->renameColumn('addr_2', 'housenumber');
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
            $table->renameColumn('street', 'addr_1');
        });
        Schema::table('user_dpas', function (Blueprint $table) {
            $table->renameColumn('housenumber', 'addr_2');
        });
    }
}
