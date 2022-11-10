<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TableVoucherActionsCreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('voucher_actions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedSmallInteger('type');
            $table->integer('units');
            $table->mediumInteger('usage_limit')->default(1);
            $table->integer('reuse_period')->nullable();
            $table->integer('reuse_type')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('voucher_actions');
    }
}
