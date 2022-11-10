<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TableCampaignInvitationsAddUnique extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (app()->runningUnitTests()) {
            return;
        }
        DB::statement('ALTER TABLE `campaign_invitations` ADD UNIQUE `campaign_id_user_id_feed_id` (`campaign_id`, `user_id`, `feed_id`)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (app()->runningUnitTests()) {
            return;
        }

        Schema::table('campaign_invitations', function (Blueprint $table) {
            $table->dropForeign('campaign_invitations_campaign_id_foreign');
            $table->dropForeign('campaign_invitations_user_id_foreign');
        });

        DB::statement('ALTER TABLE `campaign_invitations` DROP INDEX `campaign_id_user_id_feed_id`');

        Schema::table('campaign_invitations', function (Blueprint $table) {
            $table->foreign('user_id')->references('usr_id')->on('usr');
            $table->foreign('campaign_id')->references('id')->on('campaigns');
        });
    }
}
