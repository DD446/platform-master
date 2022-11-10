<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToUserOauthTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
/*        Schema::table('user_oauth', function (Blueprint $table) {
            $table->foreign('user_id')->references('usr_id')->on('usr')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });*/
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (app()->runningUnitTests()) {
            return;
        }
        /*Schema::table('user_oauth', function (Blueprint $table) {
            $table->dropForeign('user_oauth_user_id_foreign');
        });*/
    }
}
