<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolePermissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
/*        Schema::create('role_permission', function (Blueprint $table) {
            $table->integer('role_permission_id')->default(0)->primary();
            $table->integer('role_id')->default(0)->index('role_id');
            $table->integer('permission_id')->default(0)->index('permission_id');
        });*/
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('role_permission');
    }
}
