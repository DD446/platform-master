<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TableSearchLogsCreated extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('search_logs', function (Blueprint $table) {
            $table->id();
            $table->string('query');
            $table->boolean('is_boolean_search')->default(false);
            $table->string("index_name")->nullable();
            $table->string("model")->nullable();
            $table->text("ids")->nullable();
            $table->integer("hits")->default(0);
            $table->float("execution_time")->default(0);
            $table->string("driver")->nullable();;
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('search_logs');
    }
}
