<?php

namespace App\Http\Middleware;

use Closure;

class CheckForMaintenanceMode extends \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode
{

    /**
     * The URIs that should be accessible while maintenance mode is enabled.
     *
     * @var array
     */
    protected $except = ['/', 'faq', 'faq/*', 'news', 'news/*', 'terms', 'agb', 'privacy', 'player/config/*'];
}
