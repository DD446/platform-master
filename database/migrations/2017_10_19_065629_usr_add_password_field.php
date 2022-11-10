<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UsrAddPasswordField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('usr', function (Blueprint $table) {
            $table->string('remember_token')->after('passwd')->nullable();
            $table->string('password')->after('passwd')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        $sql = "UPDATE `usr` SET `created_at`=`date_created`, `updated_at`=`last_updated`";
        DB::connection()->getPdo()->exec($sql);
        $sql = "UPDATE `usr` SET `deleted_at`=`last_updated` WHERE `is_acct_active`=0";
        DB::connection()->getPdo()->exec($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('usr', function (Blueprint $table) {
            $table->dropColumn(['remember_token', 'password']);
        });
        Schema::table('usr', function (Blueprint $table) {
            $table->dropTimestamps();
        });
        Schema::table('usr', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
}
