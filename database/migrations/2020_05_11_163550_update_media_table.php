<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class UpdateMediaTable extends Migration
{
    public function up()
    {
        Schema::table('media', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->after('id');
            $table->string('conversions_disk')->nullable()->after('disk');
        });

        Media::cursor()->each(
            fn (Media $media) => $media->update(['uuid' => Str::uuid()])
        );

        $sql = "UPDATE `media` SET `conversions_disk`=`disk`";
        DB::connection()->getPdo()->exec($sql);
    }

    public function down()
    {
        Schema::table('media', function (Blueprint $table) {
            $table->dropColumn(['uuid', 'conversions_disk']);
        });
    }
}
