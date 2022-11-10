<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for all database work. Of course
    | you may use many connections at once using the Database library.
    |
    */

    'default' => env('DB_CONNECTION', 'mongodb'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the database connections setup for your application.
    | Of course, examples of configuring each database platform that is
    | supported by Laravel is shown below to make development simple.
    |
    |
    | All database work in Laravel is done through the PHP PDO facilities
    | so make sure you have the driver for your particular database of
    | choice installed on your machine before you begin development.
    |
    */

/*    'connections' => [

        'mysql' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            //'charset' => 'utf8mb4',
            //'collation' => 'utf8mb4_unicode_ci',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],
    ],*/

    'connections' => [

        'mongodb' => [
            'driver' => 'mongodb',
            'dsn' => env('DB_URI', 'mongodb+srv://username:password@<atlas-cluster-uri>/myappdb?retryWrites=true&w=majority'),
            'database' => 'myappdb',
        ],
        'sqlite' => [
            'driver' => 'sqlite',
            'database' => env('DB_DATABASE', database_path('database.sqlite')),
            'prefix' => '',
        ],

        'testing' => [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ],

        'mysql' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'podcaster'),
            'username' => env('DB_USERNAME', 'podcaster'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            //'charset' => 'utf8mb4',
            //'collation' => 'utf8mb4_unicode_ci',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],

        'statistics' => [
            'driver' => 'mysql',
            'host' => env('DB_STATS_HOST', env('DB_HOST', '127.0.0.1')),
            'port' => env('DB_STATS_PORT', env('DB_PORT', '3306')),
            'database' => env('DB_STATS_DATABASE', env('DB_DATABASE', 'podcaster')),
            'username' => env('DB_STATS_USERNAME', env('DB_USERNAME', 'podcaster')),
            'password' => env('DB_STATS_PASSWORD', env('DB_PASSWORD', '')),
            'unix_socket' => env('DB_STATS_SOCKET', env('DB_SOCKET', '')),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],

        'mongodb' => [
            'driver'   => 'mongodb',
            'host'     => env('DB_MONGO_HOST', 'localhost'),
            'port'     => env('DB_MONGO_PORT', 27017),
            'database' => env('DB_MONGO_DATABASE'),
            'username' => env('DB_MONGO_USERNAME'),
            'password' => env('DB_MONGO_PASSWORD'),
            'options'  => [
                'database' => env('DB_MONGO_AUTH_DATABASE', 'admin'), // sets the authentication database required by mongo 3
                //'replicaSet' => 'replset-name',
                //'readPreference' => MongoClient::RP_SECONDARY_PREFERRED
            ]
        ],

        'wordpress' => [ // for WordPress database (used by Corcel)
            'driver'    => 'mysql',
            'host'      => '162.55.152.159',
            'port'      => env('DB_PORT', '3306'),
            'database'  => 'podcasterblogs',
            'username'  => 'podcasterblogs',
            'password'  => 'Eojzn7!iCzCmo*W(Ff/auzh/bKiX8TlW',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
            'engine'    => null,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run in the database.
    |
    */

    'migrations' => 'migrations',

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer set of commands than a typical key-value systems
    | such as APC or Memcached. Laravel makes it easy to dig right in.
    |
    */

    'redis' => [

        'client' => 'phpredis',

        'default' => [
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', 6379),
            'database' => env('REDIS_DATABASE', 0),
        ],

        'cache' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', 6379),
            'database' => env('REDIS_CACHE_DB', 0),
        ],

        'session' => [
            'host' => env('REDIS_SESSION_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', 6379),
            'database' => env('REDIS_SESSION_DATABASE', 1),
        ],

    ],

];
