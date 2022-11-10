<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserQueueTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_queue', function (Blueprint $table) {
            $table->increments('user_queue_id');
            $table->string('username', 32);
            $table->string('email', 128);
            $table->string('hash', 32);
            $table->string('source')->nullable()->comment('Source where user came from');
            $table->boolean('package_id')->unsigned()->comment('ID of package user ordered');
            $table->char('country', 2)->default('DE');
            $table->timestamp('created_at')->nullable();
            $table->softDeletes();
        });

        if (app()->runningUnitTests()) {
            return;
        }

        Schema::table('user_queue', function (Blueprint $table) {
            $table->string('username', 32)->unique('username')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_queue');
    }
}
