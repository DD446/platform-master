<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TableCreateChanges extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('changes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->text('description');
            $table->boolean('is_public')->default(true);
            $table->unsignedMediumInteger('likes')->default(0);
            $table->unsignedMediumInteger('dislikes')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        if (app()->runningUnitTests()) {
            return;
        }
        DB::statement('ALTER TABLE changes ADD FULLTEXT INDEX `content` (title, description)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('changes');
    }
}
