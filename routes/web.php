<?php

use Illuminate\Support\Facades\Route;

Route::get('/sitemap.xml', 'SitemapXmlController@index')->name('sitemap.index');

Route::get('/{any}', function() {
    return view('snippets.index');
})->where('any', '.*');
