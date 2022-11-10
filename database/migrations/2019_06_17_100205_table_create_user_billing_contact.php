<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;


class TableCreateUserBillingContact extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_billing_contacts', function(Blueprint $table)
		{
		    $table->bigIncrements('id');
			$table->unsignedInteger('user_id');
			$table->string('first_name', 128)->nullable();
			$table->string('last_name', 128)->nullable();
			$table->string('telephone', 16)->nullable();
			$table->string('telefax', 20)->nullable()->comment('Fax number');
			$table->string('email', 128)->nullable();
            $table->boolean('bill_by_email')->default(true);
            $table->string('organisation')->nullable()->comment('Organisation of user');
            $table->string('department')->nullable()->comment('Department');
            $table->string('street', 128)->nullable();
            $table->string('housenumber', 128)->nullable();
            $table->string('city', 64)->nullable();
            $table->char('country', 2)->nullable();
            $table->string('post_code', 16)->nullable();
            $table->string('vat_id', 15)->nullable()->comment('USt-ID');
            $table->text('extras')->nullable()->comment('Zusatzangaben');
            $table->timestamps();
            $table->softDeletes();
		});

        $sql = "INSERT INTO `user_billing_contacts` (`user_id`, `first_name`, `last_name`, `telephone`, `telefax`, `email`, `organisation`, `street`, `housenumber`, `city`, `country`, `post_code`, `vat_id`)  SELECT `usr_id`, `first_name`, `last_name`, `telephone`, `telefax`, `email`, `organisation`, `addr_1`, `addr_2`, `city`, `country`, `post_code`, `vat_id` FROM usr WHERE deleted_at IS NULL AND is_acct_active=1";
        DB::connection()->getPdo()->exec($sql);
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('user_billing_contacts');
	}

}
