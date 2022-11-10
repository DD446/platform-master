<?php

namespace App\Http\Controllers;

class AppsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        \SEO::setTitle(trans('apps.page_title'));

        return view('apps');
    }
}
