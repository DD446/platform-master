<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TableAudiotakesTrackingMappingsMigrateAndDelete extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = "INSERT INTO audiotakes_ids (identifier, user_id, feed_id, created_at, updated_at) SELECT hash, user_id, feed_id, created_at, updated_at FROM audiotakes_tracking_mappings";
        DB::connection()->getPdo()->exec($sql);

        Schema::dropIfExists('audiotakes_tracking_mappings');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
