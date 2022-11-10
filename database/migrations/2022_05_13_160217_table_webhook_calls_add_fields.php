<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TableWebhookCallsAddFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('webhook_calls', function (Blueprint $table) {
            $table->string('url')->nullable();
            $table->json('headers')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('webhook_calls', function (Blueprint $table) {
            $table->dropColumn('url');
        });
        Schema::table('webhook_calls', function (Blueprint $table) {
            $table->dropColumn('headers');
        });
    }
}
