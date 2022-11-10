<?php

namespace App\Http\Controllers;

use Artesaos\SEOTools\Traits\SEOTools;

class PageController extends Controller
{
    use SEOTools;

    /**
     * Display all the static pages when authenticated
     *
     * @param string $page
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\View\View|void
     */
    public function index(string $page)
    {
        if (view()->exists("pages.{$page}")) {
            return view("pages.{$page}");
        }

        return abort(404);
    }

    public function imprint()
    {
        $this->seo()
            ->setTitle(trans('pages.title_imprint'))
            ->setDescription(trans('pages.page_description_imprint'))
            ->metatags()->addMeta(['robots' => 'noindex']);

        return view('pages.imprint');
    }

    public function privacy()
    {
        \SEO::setTitle(trans('pages.title_privacy'));
        \SEO::metatags()->addMeta(['robots' => 'noindex']);

        return view('pages.privacy');
    }

    public function terms()
    {
        \SEO::setTitle(trans('pages.title_terms'));
        \SEO::metatags()->addMeta(['robots' => 'noindex']);

        return view('pages.terms');
    }

    public function team()
    {
        \SEO::setTitle(trans('pages.title_team'));
        \SEO::metatags()->addMeta(['robots' => 'noindex']);

        return view('pages.about');
    }

    public function jobs()
    {
        \SEO::setTitle(trans('pages.title_jobs'));
        //\SEO::metatags()->addMeta(['robots' => 'noindex']);

        return view('pages.jobs');
    }

    public function press()
    {
        $this->seo()
            ->setTitle(trans('pages.title_press'))
            ->setDescription('pages.description_press');
        //\SEO::metatags()->addMeta(['robots' => 'noindex']);

        return view('pages.press');
    }
}
