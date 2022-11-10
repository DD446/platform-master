<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class StatamicAuthTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('usr', function (Blueprint $table) {
            $table->boolean('super')->default(false);
            $table->string('name')->nullable();
            $table->string('avatar')->nullable();
            $table->json('preferences')->nullable();
            $table->timestamp('last_login')->nullable();
            //$table->string('password')->nullable()->change();
        });

        Schema::create('role_user', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->string('role_id');
        });

        Schema::create('group_user', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->string('group_id');
        });
    }

    /**
     * Reverse the migrations.
     */
     public function down()
     {
         Schema::table('usr', function (Blueprint $table) {
             $table->dropColumn(['super', 'name', 'avatar', 'preferences', 'last_login']);
             //$table->string('password')->nullable(false)->change();
         });

         Schema::dropIfExists('role_user');
         Schema::dropIfExists('group_user');
     }
}
