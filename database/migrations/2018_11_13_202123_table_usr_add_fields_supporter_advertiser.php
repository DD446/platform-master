<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TableUsrAddFieldsSupporterAdvertiser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('usr', function (Blueprint $table) {
            $table->boolean('is_supporter')->default(false);
            $table->boolean('is_advertiser')->default(false);
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
            $table->dropColumn(['is_supporter', 'is_advertiser']);
        });
    }
}
