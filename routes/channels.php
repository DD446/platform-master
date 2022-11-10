<?php

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;

/*Broadcast::channel('spotify.stats.{exportId}', function ($user, $exportId) {
    return (int) $user->usr_id === (int) \App\Models\SpotifyAnalyticsExport::findOrNew($exportId)->user_id;
});*/

Broadcast::channel('state.check.{uuid}', function ($user, $uuid) {
    return user_uuid($user) === $uuid;
});

Broadcast::channel('channel.show.import.{feedId}', function ($user, $feedId) {
    return \App\Models\Feed::whereUsername($user->username)->whereFeedId($feedId)->count() === 1;
});

Broadcast::channel('webserver.reloaded.{feedId}', function ($user, $feedId) {
    return \App\Models\Feed::whereUsername($user->username)->whereFeedId($feedId)->count() === 1;
});

Broadcast::channel('stats.export.{id}', function ($user, $id) {
    return \App\Models\StatsExport::find($id)->user_id === $user->id;
});

Broadcast::channel('stats.exported.{id}', function ($user, $id) {
    return \App\Models\StatsExport::find($id)->user_id === $user->id;
});

Broadcast::channel('auphonic.production.{uuid}', function ($user, $uuid) {
    return \App\Models\WebhookSend::whereUserId($user->id)->where('payload->uuid', '=', $uuid)->count() === 1;
});
