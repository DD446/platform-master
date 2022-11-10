<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TableFaqAddFulltextIndex extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!app()->runningUnitTests()) {
            DB::statement('ALTER TABLE faq ADD FULLTEXT INDEX `qa` (question, answer)');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!app()->runningUnitTests()) {
            Schema::table('faq', function (Blueprint $table) {
                $table->dropIndex('qa');
            });
        }
    }
}
