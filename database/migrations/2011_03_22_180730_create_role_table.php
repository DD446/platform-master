<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
/*        Schema::create('role', function (Blueprint $table) {
            $table->integer('role_id')->default(-1)->primary();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->dateTime('date_created')->nullable();
            $table->integer('created_by')->nullable();
            $table->dateTime('last_updated')->nullable();
            $table->integer('updated_by')->nullable();
        });*/
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('role');
    }
}
