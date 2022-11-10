<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\Feed;
use App\Models\User;

class ServerStatusController extends Controller
{
    public function mongo()
    {
        Feed::whereUsername('beispiel')->firstOrFail();

        return 'Success';
    }

    public function mysql()
    {
        User::whereUsername('beispiel')->firstOrFail();

        return 'Success';
    }

    public function redis()
    {
        $key = 'SERVER_STATUS_TEST_REDIS';

        if (Cache::has($key)) {
            Cache::forget($key);
        }
        Cache::add($key, 'Success', 60);

        return Cache::get($key);
    }
}
