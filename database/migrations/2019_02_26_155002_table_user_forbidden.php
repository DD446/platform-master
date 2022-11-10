<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TableUserForbidden extends Migration
{
    static $aForbiddenUsernames = [
        "api",
        "podcast",
        "podcasting",
        "podcaster",
        "podcast.de",
        "podcharts",
        "charts",
        "sendung",
        "sender",
        "base",
        "blog",
        "blogs",
        "cast",
        "caster",
        "support",
        "supporter",
        "admin",
        "administration",
        "administrator",
        "postmaster",
        "webmaster",
        "email",
        "benutzer",
        "user",
        "anleitung",
        "hilfe",
        "help",
        "rss",
        "opml",
        "atom",
        "feed",
        "channel",
        "show",
        "pod",
        "podcastde",
        "podcast_de",
        "podcast-de",
        "podcasterde",
        "podcaster_de",
        "podcaster-de",
        "export",
        "type",
        "m3u",
        "pls",
        "xspf",
        "name",
        "benutzername",
        "benutzernamen",
        "test",
        "testuser",
        "passwort",
        "password",
        "gast",
        "guest",
        "anonymous",
        "anonym",
        "ftp",
        "system",
        "kommentar",
        "kommentator",
        "moderator",
        "sysadmin",
        "sysadmin",
        "root",
        "r00t",
        "r0ot",
        "ro0t",
        "podster",
        "dopcast",
        "podcast24",
        "mypoddy",
        "odeo",
        "seite",
        "kommentar",
        "MeinBenutzername",
        "mein-benutzerkonto",
        "podsu",
        "episode",
        "episoden",
        "kategorie",
        "kategorien",
        "stichwort",
        "stichwoerter",
        'cialis',
        'viagra',
        'gmbh',
        'customer',
        'customers',
        'web',
        'ww',
        'www',
        'wwww',
        'wwwww',
        'wwwwww',
        'ichbinein',
        'fabio',
        'dein',
        'mein',
        'ein',
        '123',
        '1001',
        'firma',
        'unternehmen',
        'wiki',
        'wikis',
        'twitter',
        'faq',
        'forum',
        'foren',
        'upload',
        'download',
        'seo',
        'kredit',
        'konto',
        'secure',
        'ssl',
        'devel',
        'stage',
        'video',
        'main',
        'invite',
        'files',
        'cdn',
        'dtfreunde',
        'plugin',
        'anmelden',
        'abmelden',
        'anmeldung',
        'podcasts',
        'news',
        'socket',
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_forbidden', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username')->index();
        });

        foreach (self::$aForbiddenUsernames as $username) {
            (new \App\Models\UserForbidden(['username' => $username]))->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_forbidden');
    }
}
