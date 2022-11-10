<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UsrAddPrivacyFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('usr', function (Blueprint $table) {
            $table->float('privacy_version')->after('terms_version')->nullable()->comment('Privacy policy version user accepted');
            $table->dateTime('privacy_date')->after('terms_version')->nullable()->comment('Date privacy policy was accepted');
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
            $table->dropColumn(['privacy_date', 'privacy_version']);
        });
    }
}
