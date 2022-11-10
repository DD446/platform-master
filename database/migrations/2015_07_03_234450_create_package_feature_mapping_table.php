<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackageFeatureMappingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('package_feature_mapping', function (Blueprint $table) {
            $table->unsignedMediumInteger('package_feature_mapping_id')->primary();
            $table->unsignedMediumInteger('package_feature_id');
            $table->unsignedMediumInteger('package_id');
            $table->unsignedBigInteger('units')->nullable();
            $table->tinyInteger('status')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('package_feature_mapping');
    }
}
