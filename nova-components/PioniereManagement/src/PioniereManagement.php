<?php

namespace App\PioniereManagement;

use Laravel\Nova\Nova;
use Laravel\Nova\Tool;

class PioniereManagement extends Tool
{
    /**
     * Perform any tasks that need to happen when the tool is booted.
     *
     * @return void
     */
    public function boot()
    {
        Nova::script('pioniere-management', __DIR__.'/../dist/js/tool.js');
        Nova::style('pioniere-management', __DIR__.'/../dist/css/tool.css');
    }

    /**
     * Build the view that renders the navigation links for the tool.
     *
     * @return \Illuminate\View\View
     */
    public function renderNavigation()
    {
        return view('pioniere-management::navigation');
    }
}
