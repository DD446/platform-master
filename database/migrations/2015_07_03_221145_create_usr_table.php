<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsrTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usr', function (Blueprint $table) {
            $table->increments('usr_id');
            $table->integer('role_id')->default(0);
            $table->string('username', 64)->nullable()->unique();
            $table->string('passwd', 32)->nullable();
            $table->string('first_name', 128)->nullable();
            $table->string('last_name', 128)->nullable();
            $table->string('name_title', 45)->nullable()->comment('Title of user');
            $table->string('telephone', 16)->nullable();
            $table->string('telefax', 20)->nullable()->comment('Fax number');
            $table->string('email', 128)->nullable()->unique('email');
            $table->string('url')->nullable()->comment('Website');
            $table->string('organisation')->nullable()->comment('Organisation of user');
            $table->string('addr_1', 128)->nullable();
            $table->string('addr_2', 128)->nullable();
            $table->string('addr_3', 128)->nullable();
            $table->string('city', 64)->nullable();
            $table->string('region', 32)->nullable();
            $table->char('country', 2)->nullable();
            $table->string('post_code', 16)->nullable();
            $table->char('gender', 1)->nullable()->comment('Sex of member');
            $table->smallInteger('is_acct_active')->nullable();
            $table->boolean('is_trial')->default(1)->comment('Account is in trial state');
            $table->dateTime('date_created')->nullable();
            $table->dateTime('date_trialend')->nullable();
            $table->integer('created_by')->nullable();
            $table->dateTime('last_updated')->nullable();
            $table->integer('updated_by')->nullable();
            $table->unsignedTinyInteger('package_id')->nullable()->comment('ID of package user ordered');
            $table->dateTime('terms_date')->nullable()->comment('Date terms were accepted');
            $table->float('terms_version', 10, 0)->nullable()->comment('Version of terms which user accepted');
            $table->float('funds', 10)->default(0.00)->comment('Assets a user has');
            $table->unsignedMediumInteger('forum_number_post')->default(0)->comment('Number of posts this user has written in forums');
            $table->string('register_court', 75)->nullable()->comment('Registergericht');
            $table->string('register_number', 15)->nullable()->comment('Registernummer');
            $table->string('vat_id', 15)->nullable()->comment('USt-ID');
            $table->string('board')->nullable()->comment('Vorstand');
            $table->string('chairman', 100)->nullable()->comment('Aufsichtsrat');
            $table->string('representative', 100)->nullable()->comment('Vertretungsberechtigter');
            $table->string('mediarepresentative', 100)->nullable()->comment('Verantwortlicher im Sinne vom § 10 Abs. 3 MDStV');
            $table->text('controlling_authority')->nullable()->comment('Aufsichtsbehörde');
            $table->text('additional_specifications')->nullable()->comment('Zusatzangaben');
            $table->boolean('is_updating')->unsigned()->default(0);
            $table->tinyInteger('is_blocked')->default(0);
            $table->tinyInteger('is_protected')->default(0);
            $table->integer('parent_user_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('usr');
    }
}
