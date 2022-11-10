<?php

namespace App\Http\Middleware;

use App\Classes\AuphonicManager;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    protected $addHttpCookie = false;

    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'webhooks',
        AuphonicManager::WEBHOOK_URI,
        'nova-tiptap/api/image',
    ];
}
