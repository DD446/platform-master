<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('package', function (Blueprint $table) {
            $table->unsignedMediumInteger('package_id')->primary();
            $table->string('package_name', 20)->default('');
            $table->double('monthly_cost')->nullable();
            $table->smallInteger('paying_rhythm')->default(0);
            $table->boolean('package_available')->default(1);
            $table->boolean('is_hidden')->default(0);
            $table->boolean('is_default')->default(0);
            $table->string('tld', 25);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('package');
    }
}
