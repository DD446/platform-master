<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TableCreateCustomerPreferences extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_preferences', function (Blueprint $table) {
            $table->id();
            $table->string('image_name');
            $table->unsignedInteger('user_id');
            $table->unsignedBigInteger('landing_page_id');
            $table->string('company_name');
            $table->timestamps();
        });

        Schema::table('customer_preferences', function (Blueprint $table) {
            $table->foreign('user_id')->on('usr')->references('usr_id');
            $table->foreign('landing_page_id')->on('landing_pages')->references('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_preferences');
    }
}
