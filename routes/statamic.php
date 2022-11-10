<?php
/**
 * User: fabio
 * Date: 26.11.20
 * Time: 14:55
 */
use Illuminate\Support\Facades\Route;

Route::any('/{segments?}', '\Statamic\Http\Controllers\FrontendController@index')
    /*->where('segments', '.*')*/
    ->where('segments', '^(?!backend|nova|nova-api|cp).*$')
    ->name('statamic.site');

