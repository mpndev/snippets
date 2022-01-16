<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;
use \App\Http\Controllers\API\TagsController;
use App\Http\Controllers\API\UsersController;
use \App\Http\Controllers\API\LoginController;
use \App\Http\Controllers\API\SnippetsController;
use \App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\GoogleLoginController;
use \App\Http\Controllers\API\GithubLoginController;
use \App\Http\Controllers\API\UserSettingsController;
use \App\Http\Controllers\API\UserDarkmodController;
use App\Http\Controllers\API\PasswordResetController;
use App\Http\Controllers\API\FacebookLoginController;
use App\Http\Controllers\API\PasswordForgetController;
use \App\Http\Controllers\API\FavoriteSnippetsController;
use \App\Http\Controllers\API\SnippetsActionsCopyController;

Route::get('/login/github/create', [GithubLoginController::class, 'create'])->name('api.github.create');
Route::post('/login/github/store', [GithubLoginController::class, 'store'])->name('api.github.store');

Route::get('/login/google/create', [GoogleLoginController::class, 'create'])->name('api.google.create');
Route::post('/login/google/store', [GoogleLoginController::class, 'store'])->name('api.google.store');

Route::get('/login/facebook/create', [FacebookLoginController::class, 'create'])->name('api.facebook.create');
Route::post('/login/facebook/store', [FacebookLoginController::class, 'store'])->name('api.facebook.store');

Route::post('/login', [LoginController::class, 'store'])->name('api.login.store');
Route::delete('/logout', [LoginController::class, 'destroy'])->name('api.login.destroy');
Route::post('/register', [RegisterController::class, 'store'])->name('api.register.store');

Route::post('/password-forget', [PasswordForgetController::class, 'store'])->name('password.forget');

Route::post('/password-reset', [PasswordResetController::class, 'store'])->name('password.reset');

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

Route::put('/users/{user}/darkmod', [UserDarkmodController::class, 'update'])->name('api.users.darkmod.update');

Route::get('/users', [UsersController::class, 'index'])->name('api.users.index');
Route::get('/users/{user}', [UsersController::class, 'show'])->name('api.users.show');
Route::put('/users/{user}', [UsersController::class, 'update'])->name('api.users.update');
Route::delete('/users/{user}', [UsersController::class, 'destroy'])->name('api.users.destroy');

Route::delete('/cache', function() {
    Cache::flush();
});
