<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;
use \App\Http\Controllers\API\TagsController;
use \App\Http\Controllers\API\LoginController;
use \App\Http\Controllers\API\SnippetsController;
use \App\Http\Controllers\API\RegisterController;
use \App\Http\Controllers\API\GithubLoginController;
use \App\Http\Controllers\API\UserSettingsController;
use \App\Http\Controllers\API\FavoriteSnippetsController;
use \App\Http\Controllers\API\SnippetsActionsCopyController;

Route::get('/login/github/redirect', [GithubLoginController::class, 'redirectToProvider'])->name('api.github.redirect');
Route::get('/login/github/callback', [GithubLoginController::class, 'handleProviderCallback'])->name('api.github.callback');

Route::post('/login', [LoginController::class, 'store'])->name('api.login.store');
Route::delete('/logout', [LoginController::class, 'destroy'])->name('api.login.destroy');
Route::post('/register', [RegisterController::class, 'store'])->name('api.register.store');

Route::get('/snippets', [SnippetsController::class, 'index'])->name('api.snippets.index');
Route::post('/snippets', [SnippetsController::class, 'store'])->name('api.snippets.store');
Route::get('/snippets/{snippet_id_or_slug}', [SnippetsController::class, 'show'])->name('api.snippets.show');
Route::put('/snippets/{snippet_id_or_slug}', [SnippetsController::class, 'update'])->name('api.snippets.update');
Route::delete('/snippets/{snippet_id_or_slug}', [SnippetsController::class, 'destroy'])->name('api.snippets.destroy');

Route::get('/tags', [TagsController::class, 'index'])->name('api.tags.index');
Route::post('/tags', [TagsController::class, 'store'])->name('api.tags.store');
Route::delete('/tags/{tag}', [TagsController::class, 'destroy'])->name('api.tags.destroy');

Route::post('/snippets/favorite/{snippet_id_or_slug}', [FavoriteSnippetsController::class, 'store'])->name('api.snippets.favorite.store');
Route::delete('/snippets/favorite/{snippet_id_or_slug}', [FavoriteSnippetsController::class, 'destroy'])->name('api.snippets.favorite.destroy');

Route::put('/snippets/actions/copy/{snippet_id_or_slug}', [SnippetsActionsCopyController::class, 'update'])->name('api.snippets.actions.copy.update');

Route::put('/users/{user}/settings', [UserSettingsController::class, 'update'])->name('api.users.settings.update');

Route::delete('/cache', function() {
    Cache::flush();
});
