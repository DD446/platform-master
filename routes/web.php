<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*Auth::routes();*/

Route::get('/', 'HomeController@index')->name('home');
Route::get('/pakete', 'PackageController@index')->name('packages');
Route::get('/bestellung/hinweis', 'PackageController@show')->name('package.show');
Route::get('/b/{username}/{hash}', 'PackageController@edit')->name('package.edit');
Route::put('/b/{username}/{hash}', 'PackageController@update')->name('package.update');
Route::get('/bestellung/{id}/{name}', 'PackageController@create')->name('package.order');
Route::put('/paket/{id}/bestellen', 'PackageController@store')->name('package.store');
Route::redirect('/faq/antwort-56-wie-melde-ich-meinen-podcast-beim-google-play-music-podcast-portal-fur-die-google-podcasts-app-an', '/faq/antwort-56-wie-melde-ich-meinen-podcast-beim-google-podcast-manager-und-fur-die-google-podcasts-app-an', 301);
Route::get('/faq', 'FaqController@index')->name('faq.index');
Route::get('/faq/antwort-{id}-{slug}', 'FaqController@show')->name('faq.show');
Route::get('/faq/s', 'FaqController@search')->name('faq.search');
Route::get('/faq/{slug}-{id}', 'FaqController@category')->name('faq.category')->where('id', '[0-9]+');;
Route::post('/faq/like/{id}', 'FaqController@like')->name('faq.like');
Route::post('/faq/dislike/{id}', 'FaqController@dislike')->name('faq.dislike');
Route::get('/apps', 'AppsController@index')->name('apps');
Route::redirect('/podcast-host', '/', 301)->name('lp.host');
#Route::get('/podcast-host', 'LandingPageController@podcastHost')->name('lp.host');
Route::redirect('/podcast-hoster', '/', 301)->name('lp.hoster');
#Route::get('/podcast-hoster', 'LandingPageController@podcastHoster')->name('lp.hoster');
Route::redirect('/audio-podcast-hosting', '/', 301)->name('lp.audiohosting');
#Route::get('/audio-podcast-hosting', 'LandingPageController@audioPodcastHosting')->name('lp.audiohosting');
Route::get('/video-podcast-hosting', 'LandingPageController@videoPodcastHosting')->name('lp.videohosting');
Route::redirect('/coach', '/bestellung/3/Profi', 302);
Route::get('/podcast-hosting-fuer-coaches', 'LandingPageController@coach')->name('lp.coach');
Route::get('/podcast-hosting-fuer-trainer', 'LandingPageController@trainer')->name('lp.trainer');
Route::get('/tour', 'LandingPageController@tour')->name('lp.tour');
Route::get('/hilfe', 'LandingPageController@help')->name('lp.help');
Route::get('/hilfe/videos', 'LandingPageController@videos')->name('lp.videos');
Route::get('/hilfe/video/{video}-{slug}', 'LandingPageController@video')->name('lp.video');
Route::get('/social-media', 'LandingPageController@socialmedia')->name('lp.socialmedia');
#Route::get('/blog', 'LandingPageController@blog')->name('lp.blog');
Route::get('/sitemap', 'LandingPageController@sitemap')->name('lp.sitemap');
Route::get('/kundenmeinungen', 'LandingPageController@reviews')->name('lp.reviews');
Route::get('/podcasting/{lp}-{slug}', 'LandingPageController@builder')->name('lp.builder');
Route::post('/news/like/{news}', 'NewsController@like');
Route::post('/news/dislike/{news}', 'NewsController@dislike');
Route::get('news', 'NewsController@index')->name('news.index');
Route::get('news/{id}', 'NewsController@show')->name('news.show');
#Route::post('/upload/image/news', 'NewsController@upload');

//Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');
Route::get('abmelden', 'Auth\LoginController@logout');
//Route::get('2fa', 'Auth\LoginController@twoFactorAuth');
Route::get('/password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('auth.password.reset');

Route::get('/agb', 'PageController@terms')->name('page.terms');
Route::redirect('/datenschutz', '/privacy');
Route::get('/privacy', 'PageController@privacy')->name('page.privacy');
Route::get('/impressum', 'PageController@imprint')->name('page.imprint');
Route::get('/team', 'PageController@team')->name('page.team');
Route::redirect('/jobs', 'https://www.podcast.de/jobs', 302)->name('page.jobs');
#Route::get('/jobs', 'PageController@jobs')->name('page.jobs');
Route::get('/presse', 'PageController@press')->name('page.press');

Route::get('kontakt', 'ContactUsController@create')->name('contactus.create');
Route::put('kontakt/abschicken', 'ContactUsController@store')->name('contactus.store');

Route::get('/feedback', 'FeedbackController@create')->name('feedback.create');
Route::post('/feedback', 'FeedbackController@store')->name('feedback.store');

Route::get('/player/config/{id}', 'PlayerController@config');

Route::group(['middleware' => ['auth']], function () {
    Route::get('/podcasts', 'FeedController@index')->name('feeds');
    Route::redirect('/podcast/anlegen', '/podcast/wizard');
    Route::redirect('/podcast/erstellen', '/podcast/wizard');
    //Route::get('/podcast/erstellen', 'FeedController@create')->name('podcast.create');
    Route::get('/podcast/wizard', 'FeedController@wizard')->name('podcast.wizard'); // TODO: Rename route
    Route::get('/podcast/{id}/opml', 'OpmlController@show')->name('podcast.opml.show');
    Route::get('/podcast/{feed}', 'FeedController@show')->name('podcast.show');
    Route::get('/statistiken', 'StatsController@index')->name('stats');
    Route::get('/stats/user/storage', 'StatsController@storage')->name('stats.user.storage');
    Route::get('/podcaster/podcaster/action/getStorageUsage', 'StatsController@storage');
    Route::get('/stats/subscribers/counter', 'StatsController@subscribers');
    Route::get('/podcaster/podcaster/action/getDownloads', 'StatsController@downloads');
    Route::get('/stats/listeners/counter', 'StatsController@listeners');
    Route::get('/dpa', 'PrivacyController@dpa')->name('dpa');
    Route::post('/dpa', 'PrivacyController@store')->name('dpa.store');
    Route::get('/dpa/av', 'PrivacyController@av')->name('dpa.av');
    Route::get('/dpa/tom', 'PrivacyController@tom')->name('dpa.tom');
    Route::get('/dpa/{id}', 'PrivacyController@download')->name('dpa.download');
    Route::get('/dpa/{id}/delete', 'PrivacyController@delete')->name('dpa.delete');
    Route::resource('spotify/statistiken/exports', 'SpotifyAnalyticsExportController');
    Route::get('/spotify/statistiken/files', 'SpotifyAnalyticsController@files');
    Route::post('/spotify/statistiken', 'SpotifyAnalyticsController@show');
    Route::get('/spotify/statistiken/first', 'SpotifyAnalyticsController@first');
    Route::get('/spotify/statistiken', 'SpotifyAnalyticsController@index');
    Route::delete('/spotify/delete', 'SpotifyController@delete')->name('spotify.delete');
    Route::resource('spotify', 'SpotifyController')->except(['create', 'edit']);
    Route::resource('amazon', 'AmazonController')->except(['create', 'edit']);
    Route::resource('deezer', 'DeezerController')->only(['index', 'store']);
    Route::resource('campaigns', 'CampaignController');
    Route::resource('campaign/invitations', 'CampaignInvitationController');
    Route::post('/media/copy', 'MediaController@copy');
    Route::post('/media/rename', 'MediaController@rename');
    Route::get('/media/download/{id}', 'MediaController@download');
    Route::put('/media/group', 'MediaController@setGroup')->name('media.group');
    Route::get('/media/groups', 'MediaController@groups')->name('media.groups');
    Route::get('/media/upload', 'MediaController@create')->name('media.upload');
    Route::get('/media/dropbox', 'MediaController@dropbox')->name('media.dropbox');
    Route::post('/media/load', 'MediaController@loadFromUrl')->name('media.load');
    Route::post('/media/upload', 'MediaController@store');
    Route::post('/media/upload/chunks', 'MediaController@storeChunks')->name('media.upload.chunks');
    //Route::post('/media/upload/chunks', 'DependencyUploadController@uploadFile')->name('media.upload.chunks');
    Route::get('/media/replace/{id}', 'MediaController@edit')->name('media.edit');
    Route::post('/media/replace/{id}', 'MediaController@update')->name('media.replace');
    Route::resource('/supporter', 'SupporterController');
    Route::get('/supported', 'SupportedController@index');
    Route::post('/supported', 'SupportedController@show')->name('supported.show');
    Route::get('/einstellungen', 'UserPreferenceController@index')->name('settings.index');
    Route::get('/guthaben', 'UserAccountingController@index')->name('accounting.index');
    Route::get('/guthaben/einzahlen/paypal', 'UserAccountingController@paypal');
    Route::post('/guthaben/einzahlen/paypal', 'UserAccountingController@paypal')->name('accounting.paypal');
    Route::get('/guthaben/einzahlung', 'UserAccountingController@create')->name('accounting.create');
    Route::get('/billing', 'UserBillingContactController@index');
    Route::put('/billing', 'UserBillingContactController@update');
    Route::resource('mediathek', 'MediaController');
    Route::get('/speicherplatz', 'SpaceController@index')->name('spaces.index');
    Route::redirect('/mediathek/berechnung', '/speicherplatz', 302);
    #Route::get('/mediathek/{id}', 'MediaController@show');
    Route::resource('bills', 'BillController');
    Route::get('/rechnung/{id}/herunterladen', 'BillController@download')->name('bills.download');
    Route::get('/rechnung/{id}', 'BillController@show')->name('rechnung.show');
    Route::get('/rechnungen', 'BillController@index')->name('rechnung.index');
    Route::get('/passwort/aendern', 'Auth\ChangePasswordController@index')->name('password.change');
    Route::put('/passwort/aendern', 'Auth\ChangePasswordController@update')->name('password.aendern');
    Route::get('/feedvalidator/{id}/actions/{type}', 'FeedValidatorController@actions');
    Route::post('/feedvalidator', 'FeedValidatorController@run');
    Route::get('/feed/count', 'FeedController@count')->name('feed.count');
    Route::post('/feed/logo', 'FeedLogoController@store')->name('feed.logo');
    Route::post('/feed/check', 'FeedImportController@check');
    Route::post('/feed/logo/check', 'FeedLogoController@check');
    Route::delete('/feed/logo/{feed}', 'FeedLogoController@destroy');
    Route::post('/feedsubmit', 'FeedSubmitController@show');
    Route::put('/feedsubmit', 'FeedSubmitController@store');
    Route::delete('/feedsubmit/{type}/{feed}', 'FeedSubmitController@destroy');
    Route::get('/oauth/hash/{id}', 'Auth\LoginController@show');
    Route::get('/countries', 'CountryController@index');
    //Route::get('/dienst/twitter/freigabe', 'TwitterController@oauth')->name('twitter.oauth');
    Route::post('/dienst/twitter/freigabe', 'TwitterController@oauth')->name('twitter.oauth');
    Route::get('/dienst/twitter/callback', 'TwitterController@callback')->name('twitter.callback');
    Route::post('/dienst/youtube/freigabe', 'YoutubeController@oauth')->name('youtube.oauth');
    Route::get('/dienst/youtube/callback', 'YoutubeController@callback')->name('youtube.callback');
    Route::get('/dienst/youtube', 'YoutubeController@index')->name('youtube.index');
    Route::post('/dienst/facebook/freigabe', 'FacebookController@oauth')->name('facebook.oauth');
    Route::get('/dienst/facebook/callback', 'FacebookController@callback')->name('facebook.callback');
    Route::get('/dienst/facebook', 'FacebookController@index')->name('facebook.index');
    Route::get('/pakete/kuendigung', 'PackageController@delete')->name('package.delete');
    Route::delete('/pakete/kuendigung', 'UserController@destroy')->name('user.destroy');
    Route::resource('/pakete/extras', 'PackageExtrasController');
    Route::get('/pakete/widerruf', 'PackageController@withdrawDowngrade');
    Route::put('/pakete', 'PackageController@change')->name('package.change');
    Route::get('/podcast/{id}/episode/{guid}/edit', 'ShowController@edit');
    Route::get('/podcast/{id}/episode/{guid}/share', 'ShowController@share');
    Route::get('/podcast/{id}/episode', 'ShowController@create');
    Route::delete('/feed/{feed}/show/{id}', 'ShowController@destroy');
    Route::post('/feed/{feed}/show/{id}/copy', 'ShowController@copy');
    Route::resource('/feed/{feed}/settings', 'FeedSettingController')->names('feedsettings');
    Route::resource('/player/config', 'PlayerConfigController')->except(['show', 'create']);
    Route::post('player/config/{config}/copy', 'PlayerConfigController@copy')->name('config.copy');
    Route::post('/changes/like/{change}', 'ChangeController@like');
    Route::post('/changes/dislike/{change}', 'ChangeController@dislike');
    Route::get('/changes', 'ChangeController@index');
    Route::resource('/profil', 'UserController');
    Route::resource('/pagebuilder', 'PageBuilderController');
    Route::get('/email', 'UserEmailController@index')->name('email.index');
    Route::put('/email', 'UserEmailController@create')->name('email.create');
    Route::get('/email/{hash}/aktualisieren', 'UserEmailController@update')->name('email.update');
    Route::get('/audiotakes/vertrag/anlegen', 'AudiotakesController@create')->name('audiotakes.create');
    Route::get('/audiotakes/gutschrift/{id}/herunterladen', 'AudiotakesController@creditvoucher')->name('audiotakes.creditvoucher');
    Route::get('/audiotakes/leads', 'AudiotakesController@adoptin');
    Route::get('/audiotakes/ids', 'AudiotakesController@ids');
    Route::get('/audiotakes/terms', 'AudiotakesController@show');
    Route::get('/audiotakes', 'AudiotakesController@index')->name('audiotakes.index');
    Route::get('/audiotakes/statistiken/{id}', 'AudiotakesController@stats');
    Route::post('/audiotakes', 'AudiotakesController@store');
    Route::redirect('/vermarktung', '/audiotakes');
    Route::get('/freigaben', 'ApprovalController@index')->name('approvals');
    Route::get('/podcast/{id}/episode', 'ShowController@create');
    Route::get('/podcasts#/podcast/{id}/episode/{guid}', 'ShowController@edit')->name('show.edit');
    Route::get('/roulette', 'PodcastRouletteController@index')->name('roulette.index');
    Route::post('/roulette', 'PodcastRouletteController@upload')->name('roulette.upload');
    Route::get('/roulette/add', 'PodcastRouletteController@create')->name('roulette.create');
    Route::post('/roulette/add', 'PodcastRouletteController@store')->name('roulette.store');
    Route::delete('/roulette/{id}', 'PodcastRouletteController@destroy')->name('roulette.destroy');
    Route::get('/rezension', 'ReviewController@create')->name('review.create');
    Route::put('/rezension', 'ReviewController@store')->name('review.store');
    Route::resource('voucher', 'VoucherController')->only(['update']);
    Route::resource('auphonic', 'AuphonicController')->only(['index', 'create']);

    Route::get('/roulette/admin', 'PodcastRouletteMatchController@edit')->name('roulette.match.edit');
    Route::put('/roulette/admin', 'PodcastRouletteMatchController@store')->name('roulette.match.store');
    Route::resource('/hilfe/videos/admin', 'HelpVideoAdminController', ['parameters' => ['admin' => 'video']])->names('help.video.admin')->except(['create', 'store', 'show', 'destroy']);

    // Admin
    Route::get('/backup/bills/{id}', 'BillController@downloadBackup');
    Route::get('/backup/bills', 'BillController@backups');
    Route::get('/tax/export', 'TaxAdminController@export');
    Route::get('/tax/agenda', 'TaxAdminController@agenda');
    Route::get('/feed/show/delete/duplicate', 'ShowController@showDeleteDuplicateForm');
    Route::delete('/feed/show/delete/duplicate', 'ShowController@deleteDuplicate');

    Route::group(['prefix' => 'beta'], function() {
        Route::get('/es', 'StatsController@externalSubscribers');
        Route::get('/statistiken/export/csv', 'API\StatsExportController@csv');
        #Route::resource('/statistiken/export', 'StatsExportController');
        Route::get('/stats', 'StatsController@index')->name('betastats');
        Route::resource('/payment', 'PaymentController');
        Route::redirect('/podcasts', '/podcasts');
        Route::post('/podcasts/shows', 'FeedController@shows');
        Route::get('/domains/user', 'FeedController@domains');
        Route::get('/domains/tlds', 'FeedController@tlds');
        Route::post('/domains/check', 'FeedController@check');
        Route::post('/domains/changes', 'FeedController@getChanges');
        Route::put('/domains/changes', 'FeedController@saveChanges');
        Route::put('/podcast/{id}', 'FeedController@update');
        Route::get('/podcast/state/{id}', 'FeedValidatorController@index');
        Route::get('/podcast/anmeldung/{id}', 'FeedSubmitController@index');
        Route::get('/podcasts/auswahl', 'ShowController@wizard')->name('show.wizard');
        Route::redirect('/podcast/erstellen', '/podcast/wizard');
        Route::get('/player', 'PlayerController@index');
        Route::redirect('/player/config', '/player/config');
        Route::get('/teams', 'TeamController@index')->name('teams');
        Route::get('/member/count', 'MemberController@count')->name('member.count');
        Route::get('/member/projects', 'MemberController@projects')->name('member.projects');
        Route::get('/member/login/{id}', 'MemberController@login')->name('member.login');
        Route::put('/members/invite', 'MemberQueueController@edit')->name('invite.edit');
        Route::resource('/members/invite', 'MemberQueueController')->except(['show', 'create', 'edit']);
        Route::resource('/members', 'MemberController');
        Route::get('/podcast/{id}/episode', 'ShowController@create');
        Route::get('/suggestion/images', 'ShowController@getImageSuggestions');
        Route::get('/suggestion/files', 'ShowController@getFileSuggestions');
        Route::get('/podcast/{id}/episode', 'ShowController@create')->name('show.create');
        Route::post('/show/logo', 'ShowLogoController@store');
        Route::post('/show/duration', 'ShowController@getDuration');
        Route::get('/show/guid', 'ShowController@getGuid');
        Route::get('/podcast/{feedId}/episoden', 'ShowController@index')->name('shows.index');
        Route::resource('shows', 'ShowController', ['parameters' => ['shows' => 'guid']])->except(['index', 'show', 'edit']);
        Route::get('/podcast/{feed}', 'FeedController@show');
        Route::get('/podcast/{id}/episode/{guid}/share', 'ShowController@share')->name('show.share');
    });
});

Route::get('/status/mongo', 'ServerStatusController@mongo');
Route::get('/status/mysql', 'ServerStatusController@mysql');
Route::get('/status/redis', 'ServerStatusController@redis');

Route::redirect('/anmeldung', '/login', 302);
Route::redirect('/passwort/erinnerung', '/password/reset', 301);
//Route::redirect('/passwort/aendern', '/password/reset', 301);
Route::redirect('/terms', '/agb', 302);
Route::redirect('/imprint', '/impressum', 302);
Route::redirect('/start', '/', 301);
Route::redirect('/user/login/', '/login', 302);
Route::redirect('/user/login/redir/{hash}', '/login', 302);
Route::get('/social/login/{name}', 'Auth\LoginController@showSocialLogin');
Route::get('/login/social/{type}', 'Auth\LoginController@socialLoginCallback');
Route::post('/login/social', 'Auth\LoginController@socialLogin');
Route::get('/beta/members/invite/{invite}/{id}', 'MemberQueueController@show')->name('invite.show');

Route::post('knowledge/get-ebook', 'NewsletterController@getEbook')->name('newsletter.ebook');
Route::post('knowledge/get-rcl', 'NewsletterController@getRecordingChecklist')->name('newsletter.rcl');
Route::post('knowledge/get-pc', 'NewsletterController@getPodcastConcept')->name('newsletter.pc');

Route::webhooks(\App\Classes\AuphonicManager::WEBHOOK_URI, 'auphonic');

//Route::get('/test', 'TwitterController@test');

/*Route::get('/test', function() {
    return view('teams.invite');
});*/
#Route::view('/test', 'errors.503');
#Route::get('/test/{pagebuilder}', 'PageBuilderController@show');
/*Route::get('/test', function () {
    return response()->redirectToRoute('packages')->with(['status' => trans('login.message_failure_sociallogin', ['email' => 'q@a.d'])]);
});*/

Route::get('/mail', function () {
    //$m = \App\Models\UserAccounting::findOrFail(48234);
    //$m = \App\Models\UserPayment::findOrFail(17862);
    //return (new \App\Notifications\UserPaymentNotification())->toMail($m);
    $u = \App\Models\User::find(3);
    $u->welcome_email_state = 3;
    return new \App\Mail\WelcomeWeekMailable($u);
});
