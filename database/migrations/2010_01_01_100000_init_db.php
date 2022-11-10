<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Config;

class InitDb extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //DB::unprepared(file_get_contents(base_path() . '/database/seeds/podcaster_layout.sql'));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
/*        $name = Config::get('database.connections.'.Config::get('database.default').'.database');
        DB::statement("DROP DATABASE `{$name}`");*/
    }
}
