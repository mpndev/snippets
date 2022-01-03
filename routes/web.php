<?php

use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\SitemapXmlController;

Route::get('/sitemap.xml', [SitemapXmlController::class, 'index'])->name('sitemap.index');

Route::get('/{any}', function() {
    return view('snippets.index');
})->where('any', '.*');
