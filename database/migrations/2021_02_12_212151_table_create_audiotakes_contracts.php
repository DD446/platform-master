<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TableCreateAudiotakesContracts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('audiotakes_contracts', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->string('first_name', 128);
            $table->string('last_name', 128);
            $table->string('telephone', 16)->nullable();
            $table->string('organisation')->nullable()->comment('Organisation of user');
            $table->string('street', 128);
            $table->string('housenumber', 128);
            $table->string('post_code', 16);
            $table->string('city', 64);
            $table->char('country', 2);
            $table->string('vat_id', 15)->nullable()->comment('VAT ID');
            $table->dateTime('audiotakes_date_accepted')->useCurrent();
            $table->dateTime('audiotakes_date_canceled')->nullable();
            $table->timestamps();
        });

        Schema::table('audiotakes_contracts', function (Blueprint $table) {
            $table->foreign('user_id')->references('usr_id')->on('usr');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('audiotakes_contracts');
    }
}
