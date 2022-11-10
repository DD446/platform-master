<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TableUserUploadsAddIabField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_uploads', function (Blueprint $table) {
            $table->unsignedInteger('iab_min_size')->nullable()->after('file_size');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_uploads', function (Blueprint $table) {
            $table->dropColumn('iab_min_size');
        });
    }
}
