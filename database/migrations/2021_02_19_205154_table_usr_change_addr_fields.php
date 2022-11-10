<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TableUsrChangeAddrFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('usr', function (Blueprint $table) {
            $table->renameColumn('addr_1', 'street');
        });
        Schema::table('usr', function (Blueprint $table) {
            $table->renameColumn('addr_2', 'housenumber');
        });
        Schema::table('usr', function (Blueprint $table) {
            $table->renameColumn('addr_3', 'feed_email');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('usr', function (Blueprint $table) {
            $table->renameColumn('street', 'addr_1');
        });
        Schema::table('usr', function (Blueprint $table) {
            $table->renameColumn('housenumber', 'addr_2');
        });
        Schema::table('usr', function (Blueprint $table) {
            $table->renameColumn('feed_email', 'addr_3');
        });
    }
}
