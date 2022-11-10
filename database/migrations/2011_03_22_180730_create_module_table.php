<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModuleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
/*        Schema::create('module', function (Blueprint $table) {
            $table->integer('module_id')->default(0)->primary();
            $table->smallInteger('is_configurable')->nullable();
            $table->string('name')->nullable();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('admin_uri')->nullable();
            $table->string('icon')->nullable();
            $table->text('maintainers')->nullable();
            $table->string('version', 8)->nullable();
            $table->string('license', 16)->nullable();
            $table->string('state', 8)->nullable();
        });*/
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('module');
    }
}
