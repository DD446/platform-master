<?php

/**
 * User: fabio
 * Date: 18.06.18
 * Time: 23:08
 */

// Home

use Diglactic\Breadcrumbs\Breadcrumbs;

Breadcrumbs::for('home', function ($trail) {
    $trail->push(trans('nav.home'), route('home'));
});

Breadcrumbs::for('news', function ($trail) {
    $trail->parent('home');
    $trail->push(trans('nav.news'), route('news.index'));
});

Breadcrumbs::for('contactus', function ($trail) {
    $trail->parent('home');
    $trail->push(trans('nav.contactus'), route('contactus.create'));
});

Breadcrumbs::for('announcement', function ($trail, $news) {
    $trail->parent('home');
    $trail->push(trans('nav.news'), route('news.index'));
    $trail->push($news->title, route('news.show', ['id' => $news->id]));
});

Breadcrumbs::for('spotify', function ($trail) {
    $trail->parent('home');
    $trail->push(trans('nav.feeds'), route('feeds'));
    $trail->push(trans('nav.spotify'));
});

Breadcrumbs::for('amazon', function ($trail) {
    $trail->parent('home');
    $trail->push(trans('nav.feeds'), route('feeds'));
    $trail->push(trans('nav.amazon'));
});

Breadcrumbs::for('deezer', function ($trail) {
    $trail->parent('home');
    $trail->push(trans('nav.feeds'), route('feeds'));
    $trail->push(trans('nav.deezer'));
});

Breadcrumbs::for('audiotakes', function ($trail) {
    $trail->parent('home');
    $trail->push(trans('nav.audiotakes'), route('audiotakes.index'));
});

Breadcrumbs::for('audiotakes_add_contract', function ($trail) {
    $trail->parent('audiotakes');
    $trail->push(trans('nav.audiotakes_add_contract'), route('audiotakes.index'));
});

Breadcrumbs::for('stats', function ($trail) {
    $trail->parent('home');
    $trail->push(trans('nav.statistics'), route('stats'));
});

Breadcrumbs::for('spotifystats', function ($trail) {
    $trail->parent('stats');
    $trail->push(trans('nav.statistics_spotify'), url('/statistiken'));
});

Breadcrumbs::for('help', function ($trail) {
    $trail->parent('home');
    $trail->push(trans('nav.help'), route('lp.help'));
});

Breadcrumbs::for('tour', function ($trail) {
    $trail->parent('home');
    $trail->push(trans('nav.tour'), route('lp.tour'));
});

Breadcrumbs::for('videos', function ($trail) {
    $trail->parent('help');
    $trail->push(trans('nav.videos'), route('lp.videos'));
});

Breadcrumbs::for('video', function ($trail, \App\Models\HelpVideo $video) {
    $trail->parent('videos');
    $trail->push($video->title, route('lp.video', ['video' => $video->id, 'slug' => Str::slug($video->title)]));
});

Breadcrumbs::for('faq', function ($trail) {
    $trail->parent('help');
    $trail->push(trans('nav.faq'), route('faq.index'));
});

Breadcrumbs::for('faqcat', function ($trail, $id) {
    $trail->parent('faq');
    $trail->push(trans_choice('faq.categories', $id));
});

Breadcrumbs::for('faqsearch', function ($trail, $query) {
    $trail->parent('faq');
    $trail->push(trans('faq.search_query', ['query' => $query]));
});

Breadcrumbs::for('faqitem', function ($trail, $faq) {
    $trail->parent('faq');
    $trail->push(trans_choice('faq.categories', $faq->category_id), route('faq.category', ['id' => $faq->category_id, 'slug' => Str::slug(trans_choice('faq.categories', $faq->category_id))]));
    $trail->push($faq->question);
});

Breadcrumbs::for('packages', function ($trail) {
    $trail->parent('home');
    $trail->push(trans('nav.packages'), route('packages'));
});

Breadcrumbs::for('packages_delete', function ($trail) {
    $trail->parent('packages');
    $trail->push(trans('nav.packages_delete'));
});

Breadcrumbs::for('package_extras', function ($trail) {
    $trail->parent('home');
    $trail->push(trans('nav.packages'), route('packages'));
    $trail->push(trans('nav.package_extras'));
});

Breadcrumbs::for('shows', function ($trail, $feed) {
    $trail->parent('home');
    $trail->push(trans('nav.feeds'), route('feeds'));
    $trail->push(trans('nav.feeds_channel', ['feed' => $feed->rss['title']]), route('podcast.show', ['feed' => $feed->feed_id]));
    $trail->push(trans('nav.shows'), route('shows.index', ['feedId' => $feed->feed_id]));
});

Breadcrumbs::for('show_add', function ($trail, $feed) {
    $trail->parent('shows', $feed);
    $trail->push(trans('nav.shows_add'));
});

Breadcrumbs::for('show_edit', function ($trail, $feed, $show) {
    $trail->parent('shows', $feed);
    $trail->push(trans('nav.shows_edit', ['title' => $show['title']]), route('show.edit', ['id' => $feed->feed_id, 'guid' => $show['guid']]));
});

Breadcrumbs::for('show_share', function ($trail, $feed, $show) {
    $trail->parent('show_edit', $feed, $show);
    $trail->push(trans('nav.shows_share'));
});

Breadcrumbs::for('feeds', function ($trail) {
    $trail->parent('home');
    $trail->push(trans('nav.feeds'), route('feeds'));
});

Breadcrumbs::for('show_wizard', function ($trail) {
    $trail->parent('feeds');
    $trail->push(trans('nav.show_wizard'));
});

Breadcrumbs::for('feeds_channel', function ($trail, $feed) {
    $trail->parent('home');
    $trail->push(trans('nav.feeds'), route('feeds'));
    $trail->push(trans('nav.feeds_channel', ['feed' => $feed->feed_id]));
});

Breadcrumbs::for('feeds.state', function ($trail, $feed) {
    $trail->parent('home');
    $trail->push(trans('nav.feeds'), route('feeds'));
    $trail->push($feed->rss['title'], route('podcast.show', ['feed' => $feed->feed_id]));
    $trail->push(trans('nav.feed_state_check'));
});

Breadcrumbs::for('feeds.submit', function ($trail, $feed) {
    $trail->parent('home');
    $trail->push(trans('nav.feeds'), route('feeds'));
    $trail->push($feed->rss['title'], route('podcast.show', ['feed' => $feed->feed_id]));
    $trail->push(trans('nav.feed_submit'));
});

Breadcrumbs::for('mediamanager', function ($trail) {
    $trail->parent('home');
    $trail->push(trans('nav.mediamanager'));
});

Breadcrumbs::for('apps', function ($trail) {
    $trail->parent('home');
    $trail->push(trans('nav.settings'), route('settings.index'));
    $trail->push(trans('nav.apps'));
});

Breadcrumbs::for('playerconfig', function ($trail) {
    $trail->parent('home');
    $trail->push(trans('nav.feeds'), route('feeds'));
    $trail->push(trans('nav.playerconfig'));
});

Breadcrumbs::for('settings', function ($trail) {
    $trail->parent('home');
    $trail->push(trans('nav.settings'), route('settings.index'));
});

Breadcrumbs::for('passwordchange', function ($trail) {
    $trail->parent('settings');
    $trail->push(trans('nav.password_change'));
});

Breadcrumbs::for('useremail', function ($trail) {
    $trail->parent('settings');
    $trail->push(trans('nav.user_email'));
});

Breadcrumbs::for('feedwizard', function ($trail) {
    $trail->parent('home');
    $trail->push(trans('nav.feedwizard'));
});

Breadcrumbs::for('package', function ($trail, $package) {
    $trail->parent('home');
    $trail->push(trans('nav.packages'), route('packages'));
    $trail->push(trans('nav.package', ['name' => trans_choice('package.package_name', $package->package_name)]));
#   $trail->push(trans('nav.package', ['name' => trans_choice('package.package_name', ['name' => $package->package_name])]), route('package.order', ['id' => $package->package_id, 'name' => $package->package_name]));
});

Breadcrumbs::for('bills', function ($trail) {
    $trail->parent('funds');
    $trail->push(trans('nav.bills'), route('rechnung.index'));
});

Breadcrumbs::for('bill', function ($trail, $bill) {
    $trail->parent('bills');
    $trail->push(trans('nav.bill'));
    //$trail->push(trans('nav.bill', ['id' => $bill->bill_id]));
});

Breadcrumbs::for('funds', function ($trail) {
    $trail->parent('home');
    $trail->push(trans('nav.funds'), route('accounting.index'));
});

Breadcrumbs::for('fundsadd', function ($trail) {
    $trail->parent('funds');
    $trail->push(trans('nav.funds_add'));
});

Breadcrumbs::for('teams', function ($trail) {
    $trail->parent('home');
    $trail->push(trans('nav.teams'));
});

Breadcrumbs::for('contributors', function ($trail) {
    $trail->parent('home');
    //$trail->parent('teams');
    $trail->push(trans('nav.contributors'));
});

Breadcrumbs::for('member_invitation', function ($trail) {
    $trail->parent('contributors');
    $trail->push(trans('nav.member_invitation'));
});

Breadcrumbs::for('member_choice', function ($trail) {
    $trail->parent('home');
    $trail->push(trans('nav.member_choice'));
});

Breadcrumbs::for('profile', function ($trail) {
    $trail->parent('home');
    $trail->push(trans('nav.profile'));
});

Breadcrumbs::for('sitemap', function ($trail) {
    $trail->parent('home');
    $trail->push(trans('nav.sitemap'));
});

Breadcrumbs::for('press', function ($trail) {
    $trail->parent('home');
    $trail->push(trans('nav.press'));
});

Breadcrumbs::for('imprint', function ($trail) {
    $trail->parent('home');
    $trail->push(trans('nav.imprint'));
});

Breadcrumbs::for('privacy', function ($trail) {
    $trail->parent('home');
    $trail->push(trans('nav.privacy'));
});

Breadcrumbs::for('aboutus', function ($trail) {
    $trail->parent('home');
    $trail->push(trans('nav.aboutus'));
});

Breadcrumbs::for('terms', function ($trail) {
    $trail->parent('home');
    $trail->push(trans('nav.terms'));
});

Breadcrumbs::for('reviews', function ($trail) {
    $trail->parent('home');
    $trail->push(trans('nav.reviews'));
});

Breadcrumbs::for('approvals', function ($trail) {
    $trail->parent('profile');
    $trail->push(trans('nav.approvals'));
});

Breadcrumbs::for('lp', function ($trail, $lp) {
    $trail->parent('home');
    $trail->push($lp->title);
});
