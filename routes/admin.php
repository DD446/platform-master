<?php
use Illuminate\Support\Facades\Route;

//Route::view('/home','admin.home')->name('admin.home');
Route::get('home','Admin\HomeController@index')->name('home');

Route::resource('reviews', 'ReviewController');

Route::put('loginAs/{id}', 'Admin\HomeController@loginAs')->where('id', '[1-9]+[0-9]*')->name('loginas');