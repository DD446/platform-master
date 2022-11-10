<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/


#Route::apiResource('stats.counter', 'API\StatsCounterController')->middleware(['auth:api', 'scopes:stats,read-only-stats']);

Route::group(["middleware" => "auth:api", "scopes:stats,read-only-stats"], function() {
    Route::post('/stats/listeners', 'API\StatsListenerController@index');
    Route::get('/stats/subscribers', 'API\StatsSubscriberController@index');
    Route::get('/stats/subscribers/services', 'API\StatsSubscriberController@extern');
    Route::get('/stats/counter', 'API\StatsCounterController@index');
    Route::post('/stats/subscribers/external', 'API\StatsExternalSubscribersController@index');
    Route::get('/stats/shows', 'API\StatsShowsController@index');
    Route::get('/stats/shows/top', 'API\StatsCounterController@topshows');
    Route::get('/stats/shows/last', 'API\StatsCounterController@lastshows');
    Route::get('/stats/shows/charts', 'API\StatsCounterController@charts');
    Route::get('/stats/pioniere/earnings', 'API\StatsPioniereController@earnings');
    Route::get('/stats/pioniere/summary', 'API\StatsPioniereController@summary');
    Route::get('/stats/pioniere/hits', 'API\StatsPioniereController@hits');
    Route::get('/stats/pioniere/region', 'API\StatsPioniereController@region');
    Route::get('/stats/pioniere/demographic', 'API\StatsPioniereController@demographic');
    Route::get('/stats/pioniere/contracts', 'API\StatsPioniereController@contracts');
    Route::post('/feed/url/check', 'API\FeedController@isUrlAllowed');
    Route::apiResource('/stats/export', 'API\StatsExportController');
    Route::apiResource('/statistics/listeners', 'API\ListenerStatisticsController')->only(['index']);
    Route::apiResource('/show/templates', 'API\ShowTemplateController')->only(['index', 'store']);
});

Route::apiResource('media', 'API\MediaController', ['parameters' => ['media' => 'media_id']])->middleware(['auth:api', 'scopes:media,read-only-media'])->only(['index', 'show']);
Route::apiResource('media', 'API\MediaController', ['parameters' => ['media' => 'media_id']])->middleware(['auth:api', 'scope:media'])->except(['index', 'show']);
Route::post('/media/{media_id}/copy', 'MediaController@copy')->middleware(['auth:api', 'scope:media']);
Route::get('/media/{id}/metadata', 'API\MetadataController@show')->middleware(['auth:api', 'scope:media,read-only-media']);
Route::put('/media/{id}/metadata', 'API\MetadataController@update')->middleware(['auth:api', 'scope:media']);
Route::apiResource('feeds', 'API\FeedController', ['parameters' => ['feeds' => 'feed_id']])->middleware(['auth:api', 'scopes:feeds,read-only-feeds'])->only(['index', 'show']);
Route::apiResource('feeds', 'API\FeedController', ['parameters' => ['feeds' => 'feed_id']])->middleware(['auth:api', 'scope:feeds'])->except(['index', 'show']);
Route::get('/feeds/{feed_id}/defaults', 'API\FeedController@getDefaults')->middleware(['auth:api', 'scope:feeds']);
Route::post('/feeds/{feed_id}/copy', 'API\FeedController@copy')->middleware(['auth:api', 'scope:feeds']);
Route::get('/shows', 'API\ShowController@combined')->middleware(['auth:api', 'scopes:shows,read-only-shows']);
Route::get('/feed/{feed_id}/shows', 'API\ShowController@index')->middleware(['auth:api', 'scopes:shows,read-only-shows']);
#Route::get('/user/{username}/feed/{feed_id}/show/{guid}/share', 'API\ShowController@share')->name('api.shows.show.share');
Route::get('/feed/{feed_id}/show/{guid}', 'API\ShowController@show')->middleware(['auth:api', 'scope:shows']);
Route::put('/feed/{feed_id}/show/{guid}', 'API\ShowController@update')->middleware(['auth:api', 'scope:shows']);
Route::delete('/feed/{feed_id}/show/{guid}', 'API\ShowController@destroy')->middleware(['auth:api', 'scope:shows']);
Route::post('/feed/{feed_id}/show/{guid}/copy', 'API\ShowController@copy')->middleware(['auth:api', 'scope:shows']);
Route::post('/feed/{feed_id}/show/{guid}/move', 'API\ShowController@move')->middleware(['auth:api', 'scope:shows']);
Route::apiResource('user', 'API\UserController')->middleware(['auth:api', 'scope:read-only-user'])->only(['index', 'update']);
Route::apiResource('teams', 'API\TeamController', ['parameters' => ['teams' => 'team_id']])->middleware(['auth:api', 'scopes:teams,read-only-teams'])->only(['index', 'show']);
Route::apiResource('teams', 'API\TeamController', ['parameters' => ['teams' => 'team_id']])->middleware(['auth:api', 'scope:teams'])->except(['index', 'show']);
Route::post('/teams/{team_id}/copy', 'API\TeamController@copy')->middleware(['auth:api', 'scope:teams']);
Route::apiResource('members', 'API\MemberController', ['parameters' => ['members' => 'member_id'], 'as' => 'api'])->middleware(['auth:api', 'scopes:teams,read-only-teams,members,read-only-members'])->only(['index', 'show']);
Route::apiResource('members', 'API\MemberController', ['parameters' => ['members' => 'member_id'], 'as' => 'api'])->middleware(['auth:api', 'scopes:teams,members'])->except(['index', 'show', 'update']);
Route::get('/stats/shows/{id}', 'StatsController@shows')->middleware('auth:api');
Route::get('/stats/ad-opt-in', 'FeedController@adoptin')->middleware('auth:api');
Route::get('/stats/sources', 'API\FeedController@sources')->middleware('auth:api');
Route::get('/list/lang', 'API\ListController@lang')->middleware('auth:api');
Route::get('/list/itunes', 'API\ListController@itunes')->middleware('auth:api');
Route::get('/amazon/get-updated-shows', 'AmazonController@fetch');
Route::get('/faq/search', 'API\FaqController@search');
Route::get('/mail/welcome/unsubscribe/{user_id}/{hash}', 'API\MailController@welcomeUnsubscribe')->name('api.mail.welcome');
Route::apiResource('approvals', 'API\ApprovalController', ['parameters' => ['approval' => 'id']])->middleware('auth:api')->only(['index', 'destroy']);
Route::apiResource('payouts', 'API\AudiotakesPayoutController')->middleware('auth:api')->only(['store']);
Route::apiResource('payouts/contacts', 'API\AudiotakesPayoutContactController')->middleware('auth:api')->names('payouts.contacts');
Route::apiResource('contract/partners', 'API\AudiotakesContractPartnerController')->middleware('auth:api')->names('contract.partners')->only(['index', 'store']);
//Route::apiResource('auphonic', 'API\AuphonicController')->middleware('auth:api')->only(['index']);
Route::apiResource('auphonic/presets', 'API\AuphonicPresetController')->middleware('auth:api')->only(['index', 'show', 'store', 'update']);
//Route::get('/config/ppp/{id}/{feed}', 'FeedController@ppp')->name('config.ppp');
