<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\PlayerConfig;
use App\Models\User;

class PlayerController extends Controller
{
    public function index()
    {
        return view('player.index');
    }

    public function channel($username, $feedId)
    {
        $userId = User::select('usr_id')->whereUsername($username)->value('usr_id');

        // TODO
        // Cache requests - write static files - use nginx to check if file exists
/*        if (!$userId) {
            $pc = $fail;
        }*/
        $pc = new PlayerConfig();
        $pc->id = PlayerConfig::TYPE_CHANNEL . '~' . $username . '~' . $feedId;
        $pc->player_type = 1;
        $pc->player_configurable_id = $feedId;
        $pc->player_configurable_type = PlayerConfig::TYPE_CHANNEL;
        $pc->feed_id = $feedId;
        $pc->user_id = $userId;

        return response($pc->get($username))->header('Content-Type', 'application/javascript');
    }

    /**
     * @param  string  $username
     * @param  string  $feedId
     * @param  string  $guid
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function show(string $username, string $feedId, string $guid)
    {
        $userId = User::select('usr_id')->whereUsername($username)->value('usr_id');

        // TODO
/*        if (!$userId) {
            $pc = $fail;
        }*/
        $pc = new PlayerConfig();
        $pc->id = PlayerConfig::TYPE_SHOW . '~' . $username . '~' . $feedId . '~' . $guid;
        $pc->player_type = 1;
        $pc->player_configurable_id = $guid;
        $pc->player_configurable_type = PlayerConfig::TYPE_SHOW;
        $pc->feed_id = $feedId;
        $pc->hide_playlist_in_singlemode = true;
        $pc->user_id = $userId;

        return response($pc->get($username))->header('Content-Type', 'application/javascript');
    }

    public function config($uuid)
    {
        if (Str::endsWith($uuid, ['.js', '.css'])) {
            if (Str::endsWith($uuid, ['.css'])) {
                $contentType = 'text/css';
            } else {
                $contentType = 'application/javascript';
            }
            $uuid = Str::beforeLast($uuid, '.');
        }
        if (Str::startsWith($uuid, [PlayerConfig::TYPE_DIRECT, PlayerConfig::TYPE_SHOW, PlayerConfig::TYPE_CHANNEL])) {
            $values = explode('~', $uuid);
            $type = array_shift($values);
            switch ($type) {
                case PlayerConfig::TYPE_SHOW :
                    if (count($values) <> 3) {
                        abort(404);
                    }
                    list($username, $feedId, $guid) = $values;
                    return $this->show($username, $feedId, $guid);
                case PlayerConfig::TYPE_CHANNEL :
                    if (count($values) <> 2) {
                        abort(404);
                    }
                    list($username, $feedId) = $values;
                    return $this->channel($username, $feedId);
                case PlayerConfig::TYPE_DIRECT :
                default :
                    abort(404);
            }
        }
        $pc = PlayerConfig::whereUuid($uuid)->first();

        // TODO
        if (!$pc) {
            //$pc = $fail;
            abort(404);
        }

        return response($pc->get())->header('Content-Type', $contentType);
    }
}
