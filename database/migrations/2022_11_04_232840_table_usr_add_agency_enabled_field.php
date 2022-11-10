<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TableUsrAddAgencyEnabledField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('usr', function (Blueprint $table) {
            $table->boolean('agency_enabled')->after('last_login')->default(false);
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
            $table->dropColumn('agency_enabled');
        });
    }
}
