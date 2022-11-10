<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

class TableCreateMemberQueue extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_queue', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('email');
            $table->string('hash', 32);
            $table->bigInteger('team_id')->unsigned();
            $table->integer('role_id')->default(User::ROLE_TEAM);
            $table->timestamps();
            $table->unique(['email', 'team_id', 'role_id']);
        });

        Schema::table('member_queue', function (Blueprint $table) {
            $table->foreign('team_id')->references('id')->on('teams');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('member_queue');
    }
}
