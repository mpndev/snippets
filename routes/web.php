<?php

use App\Repositories\SnippetsRepository;
use Illuminate\Support\Facades\Route;

Route::get('/{any}', function() {
    $snippets = SnippetsRepository::getWithAllConstraints();
    return view('snippets.index', compact('snippets'));
})->where('any', '.*');
