<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\HelpVideo;
use App\Models\LandingPage;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class LandingPageController extends Controller
{
    use \Artesaos\SEOTools\Traits\SEOTools;

    public function podcastHost()
    {
        $title = trans('pages.page_title_podcasthost');
        $pageDescription = trans('pages.page_description_podcasthost');
        $this->seo()
            ->setTitle($title)
            ->setDescription($pageDescription);

        $mainEntity = [];
        $c = new \stdClass();
        $c->{'@type'} = "WebPage";
        $c->{'@id'} = route('lp.host');
        $c->image = asset('images1/podcaster_logo.svg');
        $c->name = $title;
        $mainEntity[] = $c;

        $author = [];
        $a = new \stdClass();
        $a->{'@type'} = "Author";
        $a->name = "Fabio Bacigalupo & Team";
        $author[] = $a;

        $publisher = [];
        $p = new \stdClass();
        $p->{'@type'} = "Organization";
        $p->name = config('app.name');
        $l = new \stdClass();
        $l->{'@type'} = 'ImageObject';
        $l->url = asset('images1/podcaster_logo.svg');
        $p->logo = $l;
        $publisher[] = $p;

        $this->seo()->jsonLdMulti()
            ->setType('Article')
            ->addValue('headline', $title)
            ->addValue('datePublished', '2021-03-01')
            ->addValue('dateModified', '2021-03-19')
            ->addImage(asset('images1/podcaster_logo.svg'))
            ->setDescription($pageDescription)
            ->addValues(['mainEntity' => $mainEntity, 'author' => $author, 'publisher' => $publisher]);

        if (!$this->seo()->jsonLdMulti()->isEmpty()) {
            $itemList = [];

            $c = new \stdClass();
            $c->{'@type'} = "ListItem";
            $c->position = 2;
            $i = new \stdClass();
            $i->{'@id'} = route('lp.host');
            $i->name = $title;
            $i->image = asset('images1/podcaster_logo.svg');
            $c->item = $i;
            $itemList[] = $c;

            $this->seo()->jsonLdMulti()->newJsonLd()->setType('BreadcrumbList')->setTitle('Breadcrumb')->addValues(['itemListElement' => $itemList]);
        }

        return view('landingpages.podcasthost');
    }

    public function podcastHoster()
    {
        $title = trans('pages.page_title_hoster');
        $pageDescription = trans('pages.page_description_hoster');
        $this->seo()->setTitle($title);
        $this->seo()->setDescription($pageDescription);

        $mainEntity = [];
        $c = new \stdClass();
        $c->{'@type'} = "WebPage";
        $c->{'@id'} = route('lp.hoster');
        $c->image = asset('images1/podcaster_logo.svg');
        $c->name = $title;
        $mainEntity[] = $c;

        $author = [];
        $a = new \stdClass();
        $a->{'@type'} = "Author";
        $a->name = "Fabio Bacigalupo & Team";
        $author[] = $a;

        $publisher = [];
        $p = new \stdClass();
        $p->{'@type'} = "Organization";
        $p->name = config('app.name');
        $l = new \stdClass();
        $l->{'@type'} = 'ImageObject';
        $l->url = asset('images1/podcaster_logo.svg');
        $p->logo = $l;
        $publisher[] = $p;

        $this->seo()
            ->jsonLdMulti()
            ->setType('Article')
            ->addValue('headline', $title)
            ->addValue('datePublished', '2021-03-01')
            ->addValue('dateModified', '2021-03-19')
            ->addImage(asset('images1/podcaster_logo.svg'))
            ->setDescription($pageDescription)
            ->addValues(['mainEntity' => $mainEntity, 'author' => $author, 'publisher' => $publisher]);

        if (!$this->seo()->jsonLdMulti()->isEmpty()) {
            $itemList = [];

            $c = new \stdClass();
            $c->{'@type'} = "ListItem";
            $c->position = 2;
            $i = new \stdClass();
            $i->{'@id'} = route('lp.hoster');
            $i->name = $title;
            $i->image = asset('images1/podcaster_logo.svg');
            $c->item = $i;
            $itemList[] = $c;

            $this->seo()->jsonLdMulti()->newJsonLd()->setType('BreadcrumbList')->setTitle('Breadcrumb')->addValues(['itemListElement' => $itemList]);
        }

        return view('landingpages.podcasthoster');
    }

    public function audioPodcastHosting()
    {
        $title = trans('pages.page_title_audiohosting');
        $pageDescription = trans('pages.page_description_audiohosting');
        $this->seo()->setTitle($title);
        $this->seo()->setDescription($pageDescription);

        $mainEntity = [];
        $c = new \stdClass();
        $c->{'@type'} = "WebPage";
        $c->{'@id'} = route('lp.audiohosting');
        $c->image = asset('images1/podcaster_logo.svg');
        $c->name = $title;
        $mainEntity[] = $c;

        $author = [];
        $a = new \stdClass();
        $a->{'@type'} = "Author";
        $a->name = "Fabio Bacigalupo & Team";
        $author[] = $a;

        $publisher = [];
        $p = new \stdClass();
        $p->{'@type'} = "Organization";
        $p->name = config('app.name');
        $l = new \stdClass();
        $l->{'@type'} = 'ImageObject';
        $l->url = asset('images1/podcaster_logo.svg');
        $p->logo = $l;
        $publisher[] = $p;

        $this->seo()->jsonLdMulti()
            ->setType('Article')
            ->addValue('headline', $title)
            ->addValue('datePublished', '2021-03-01')
            ->addValue('dateModified', '2021-03-19')
            ->addImage(asset('images1/podcaster_logo.svg'))
            ->setDescription($pageDescription)
            ->addValues(['mainEntity' => $mainEntity, 'author' => $author, 'publisher' => $publisher]);

        if (!$this->seo()->jsonLdMulti()->isEmpty()) {
            $itemList = [];

            $c = new \stdClass();
            $c->{'@type'} = "ListItem";
            $c->position = 2;
            $i = new \stdClass();
            $i->{'@id'} = route('lp.audiohosting');
            $i->name = $title;
            $i->image = asset('images1/podcaster_logo.svg');
            $c->item = $i;
            $itemList[] = $c;

            $this->seo()->jsonLdMulti()->newJsonLd()->setType('BreadcrumbList')->setTitle('Breadcrumb')->addValues(['itemListElement' => $itemList]);
        }

        return view('landingpages.audiopodcasthosting');
    }

    public function videoPodcastHosting()
    {
        $title = trans('pages.page_title_videohosting');
        $pageDescription = trans('pages.page_description_videohosting');
        $this->seo()->setTitle($title);
        $this->seo()->setDescription($pageDescription);

        $mainEntity = [];
        $c = new \stdClass();
        $c->{'@type'} = "WebPage";
        $c->{'@id'} = route('lp.videohosting');
        $c->image = asset('images1/podcaster_logo.svg');
        $c->name = $title;
        $mainEntity[] = $c;

        $author = [];
        $a = new \stdClass();
        $a->{'@type'} = "Author";
        $a->name = "Fabio Bacigalupo & Team";
        $author[] = $a;

        $publisher = [];
        $p = new \stdClass();
        $p->{'@type'} = "Organization";
        $p->name = config('app.name');
        $l = new \stdClass();
        $l->{'@type'} = 'ImageObject';
        $l->url = asset('images1/podcaster_logo.svg');
        $p->logo = $l;
        $publisher[] = $p;

        $this->seo()->jsonLdMulti()
            ->setType('Article')
            ->addValue('headline', $title)
            ->addValue('datePublished', '2021-03-01')
            ->addValue('dateModified', '2021-03-19')
            ->addImage(asset('images1/podcaster_logo.svg'))
            ->setDescription($pageDescription)
            ->addValues(['mainEntity' => $mainEntity, 'author' => $author, 'publisher' => $publisher]);

        if (!$this->seo()->jsonLdMulti()->isEmpty()) {
            $itemList = [];

            $c = new \stdClass();
            $c->{'@type'} = "ListItem";
            $c->position = 2;
            $i = new \stdClass();
            $i->{'@id'} = route('lp.videohosting');
            $i->name = $title;
            $i->image = asset('images1/podcaster_logo.svg');
            $c->item = $i;
            $itemList[] = $c;

            $this->seo()->jsonLdMulti()->newJsonLd()->setType('BreadcrumbList')->setTitle('Breadcrumb')->addValues(['itemListElement' => $itemList]);
        }

        return view('landingpages.videopodcasthosting');
    }

    public function help()
    {
        $title = trans('pages.page_title_help');
        $pageDescription = trans('pages.page_description_help');
        $this->seo()->setTitle($title);
        $this->seo()->setDescription($pageDescription);

        $mainEntity = [];
        $c = new \stdClass();
        $c->{'@type'} = "WebPage";
        $c->{'@id'} = route('lp.tour');
        $c->image = asset('images1/podcaster_logo.svg');
        $c->name = $title;
        $mainEntity[] = $c;

        $author = [];
        $a = new \stdClass();
        $a->{'@type'} = "Author";
        $a->name = "Fabio Bacigalupo & Team";
        $author[] = $a;

        $publisher = [];
        $p = new \stdClass();
        $p->{'@type'} = "Organization";
        $p->name = config('app.name');
        $l = new \stdClass();
        $l->{'@type'} = 'ImageObject';
        $l->url = asset('images1/podcaster_logo.svg');
        $p->logo = $l;
        $publisher[] = $p;

        $this->seo()
            ->jsonLdMulti()
            ->setType('Article')
            ->addValue('headline', $title)
            ->addValue('datePublished', '2021-03-01')
            ->addValue('dateModified', '2021-03-19')
            ->addImage(asset('images1/podcaster_logo.svg'))
            ->setDescription($pageDescription)
            ->addValues(['mainEntity' => $mainEntity, 'author' => $author, 'publisher' => $publisher]);

        if (!$this->seo()->jsonLdMulti()->isEmpty()) {
            $itemList = [];

            $c = new \stdClass();
            $c->{'@type'} = "ListItem";
            $c->position = 2;
            $i = new \stdClass();
            $i->{'@id'} = route('lp.tour');
            $i->name = $title;
            $i->image = asset('images1/podcaster_logo.svg');
            $c->item = $i;
            $itemList[] = $c;

            $this->seo()->jsonLdMulti()->newJsonLd()->setType('BreadcrumbList')->setTitle('Breadcrumb')->addValues(['itemListElement' => $itemList]);
        }

        $videos = HelpVideo::where('is_public', '=', true)->get(['username', 'title', 'poster', 'id'])->map(function ($video) {
            $video->image = $video->getLink('poster');
            $video->link = route('lp.video', ['video' => $video['id'], 'slug' => Str::slug($video['title'])]);
            return $video;
        })->toArray();

        $aCategories = (new Faq())->getCategories();

        return view('landingpages.help', compact('videos', 'aCategories'));
    }

    public function videos()
    {
        $this->seo()
            ->setTitle(trans('pages.page_title_videos'))
            ->setDescription(trans('pages.page_description_videos'));

        $videos = HelpVideo::where('is_public', '=', true)->paginate();

        return view('pages.help.video.index', compact('videos'));
    }

    /**
     * @param  HelpVideo  $video
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function video(HelpVideo $video)
    {
        $this->seo()
            ->setTitle($video->page_title)
            ->setDescription($video->page_description);

        $mainEntity = [];
        $c = new \stdClass();
        $c->{'@type'} = "Video";
        $c->{'@id'} = route('lp.video', ['video' => $video->id, 'slug' => Str::slug($video->title)]);
        $c->image = $video->getLink('poster');
        $c->name = $video->title;
        $mainEntity[] = $c;

        $author = [];
        $a = new \stdClass();
        $a->{'@type'} = "Author";
        $a->name = "Fabio Bacigalupo & Team";
        $author[] = $a;

        $publisher = [];
        $p = new \stdClass();
        $p->{'@type'} = "Organization";
        $p->name = config('app.name');
        $l = new \stdClass();
        $l->{'@type'} = 'ImageObject';
        $l->url = asset('images1/podcaster_logo.svg');
        $p->logo = $l;
        $publisher[] = $p;

/*        $this->seo()->jsonLdMulti()
            ->setType('Article')
            ->addValue('headline', $title)
            ->addValue('datePublished', '2021-03-01')
            ->addValue('dateModified', '2021-03-19')
            ->addImage(asset('images1/podcaster_logo.svg'))
            ->setDescription($pageDescription)
            ->addValues(['mainEntity' => $mainEntity, 'author' => $author, 'publisher' => $publisher]);

        if (!$this->seo()->jsonLdMulti()->isEmpty()) {
            $itemList = [];

            $c = new \stdClass();
            $c->{'@type'} = "ListItem";
            $c->position = 2;
            $i = new \stdClass();
            $i->{'@id'} = route('lp.tour');
            $i->name = $title;
            $i->image = asset('images1/podcaster_logo.svg');
            $c->item = $i;
            $itemList[] = $c;

            $this->seo()->jsonLdMulti()->newJsonLd()->setType('BreadcrumbList')->setTitle('Breadcrumb')->addValues(['itemListElement' => $itemList]);
        }*/

        $params = ['video' => $video];

        if (\request('h') == 1) {
            $params['hideNav'] = true;
        }

        return view('pages.help.video.show', $params);
    }

    public function tour()
    {
        $title = trans('pages.page_title_tour');
        $pageDescription = trans('pages.page_description_tour');
        $this->seo()
            ->setTitle($title)
            ->setDescription($pageDescription);

        $mainEntity = [];
        $c = new \stdClass();
        $c->{'@type'} = "WebPage";
        $c->{'@id'} = route('lp.tour');
        $c->image = asset('images1/podcaster_logo.svg');
        $c->name = $title;
        $mainEntity[] = $c;

        $author = [];
        $a = new \stdClass();
        $a->{'@type'} = "Author";
        $a->name = "Fabio Bacigalupo & Team";
        $author[] = $a;

        $publisher = [];
        $p = new \stdClass();
        $p->{'@type'} = "Organization";
        $p->name = config('app.name');
        $l = new \stdClass();
        $l->{'@type'} = 'ImageObject';
        $l->url = asset('images1/podcaster_logo.svg');
        $p->logo = $l;
        $publisher[] = $p;

        $this->seo()
            ->jsonLdMulti()
            ->setType('Article')
            ->addValue('headline', $title)
            ->addValue('datePublished', '2021-03-01')
            ->addValue('dateModified', '2021-11-13')
            ->addImage(asset('images1/podcaster_logo.svg'))
            ->setDescription($pageDescription)
            ->addValues(['mainEntity' => $mainEntity, 'author' => $author, 'publisher' => $publisher]);

        if (!$this->seo()->jsonLdMulti()->isEmpty()) {
            $itemList = [];

            $c = new \stdClass();
            $c->{'@type'} = "ListItem";
            $c->position = 2;
            $i = new \stdClass();
            $i->{'@id'} = route('lp.tour');
            $i->name = $title;
            $i->image = asset('images1/podcaster_logo.svg');
            $c->item = $i;
            $itemList[] = $c;

            $this->seo()
                ->jsonLdMulti()
                ->newJsonLd()
                ->setType('BreadcrumbList')
                ->setTitle('Breadcrumb')
                ->addValues(['itemListElement' => $itemList]);
        }

        $reviews = Cache::get(Review::REVIEW_CACHE_KEY_LIST_WITH_LOGO, []);
        $rc = 4;
        $reviews = Arr::random($reviews, count($reviews) < $rc ? count($reviews) : $rc, true);

        return view('landingpages.tour', ['reviews' => $reviews]);
    }

    public function coach()
    {
        $title = trans('pages.page_title_coach');
        $pageDescription = trans('pages.page_description_coach');
        $this->seo()
            ->setTitle($title)
            ->setDescription($pageDescription);
        return view('landingpages.coach');
    }

    public function trainer()
    {
        $title = trans('pages.page_title_trainer');
        $pageDescription = trans('pages.page_description_trainer');
        $this->seo()
            ->setTitle($title)
            ->setDescription($pageDescription);

        return view('landingpages.trainer');
    }

    public function blog()
    {
        $title = trans('pages.page_title_blog');
        $pageDescription = trans('pages.page_description_blog');
        $this->seo()
            ->setTitle($title)
            ->setDescription($pageDescription);

        return view('landingpages.blog');
    }

    public function sitemap()
    {
        $title = trans('pages.page_title_sitemap');
        $pageDescription = trans('pages.page_description_sitemap');
        $this->seo()
            ->setTitle($title)
            ->setDescription($pageDescription);

        return view('landingpages.sitemap');
    }

    public function reviews()
    {
        $title = trans('pages.page_title_reviews');
        $pageDescription = trans('pages.page_description_reviews');
        $this->seo()
            ->setTitle($title)
            ->setDescription($pageDescription);

        $reviews = Cache::get(Review::REVIEW_CACHE_KEY_LIST_WITH_LOGO, []);

        return view('landingpages.reviews', compact('reviews'));
    }

    /**
     * @param  LandingPage  $lp
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function builder(LandingPage $lp)
    {
        $this->seo()
            ->setTitle($lp->page_title)
            ->setDescription($lp->page_description);

        return view('landingpages.builder', compact('lp'));
    }
}
