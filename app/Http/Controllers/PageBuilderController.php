<?php

namespace App\Http\Controllers;

use Artesaos\SEOTools\Traits\SEOTools;
use Illuminate\Http\Request;
use App\Models\Feed;

class PageBuilderController extends Controller
{
    use SEOTools;

    public function index()
    {
        return view('pagebuilder.index');
    }

    public function show($pagebuilder)
    {
        $feedId = \request('feedId');
        $feed = Feed::owner()/*->whereFeedId($feedId)*/->firstOrFail();
        $shows = $feed->entries;

        $this->seo()
            ->setTitle($feed->rss['title'])
            ->setDescription($feed->rss['description']);

        $pageTitle = $feed->rss['title'];

        $rssUrl = get_feed_uri($feed->feed_id, $feed->domain);

        return view('pagebuilder.layout' . $pagebuilder . '.home', compact(['feed', 'shows', 'pageTitle', 'rssUrl']));
    }
}
