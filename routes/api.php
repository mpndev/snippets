<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;

Route::post('/login', 'API\LoginController@store')->name('api.login.store');
Route::delete('/logout', 'API\LoginController@destroy')->name('api.login.destroy');
Route::post('/register', 'API\RegisterController@store')->name('api.register.store');

Route::get('/snippets', 'API\SnippetsController@index')->name('api.snippets.index');
Route::post('/snippets', 'API\SnippetsController@store')->name('api.snippets.store');
Route::get('/snippets/{snippet_id_or_slug}', 'API\SnippetsController@show')->name('api.snippets.show');
Route::put('/snippets/{snippet_id_or_slug}', 'API\SnippetsController@update')->name('api.snippets.update');
Route::delete('/snippets/{snippet_id_or_slug}', 'API\SnippetsController@destroy')->name('api.snippets.destroy');

Route::get('/tags', 'API\TagsController@index')->name('api.tags.index');
Route::post('/tags', 'API\TagsController@store')->name('api.tags.store');
Route::delete('/tags/{tag}', 'API\TagsController@destroy')->name('api.tags.destroy');

Route::post('/snippets/favorite/{snippet_id_or_slug}', 'API\FavoriteSnippetsController@store')->name('api.snippets.favorite.store');
Route::delete('/snippets/favorite/{snippet_id_or_slug}', 'API\FavoriteSnippetsController@destroy')->name('api.snippets.favorite.destroy');

Route::put('/snippets/actions/copy/{snippet_id_or_slug}', 'API\SnippetsActionsCopyController@update')->name('api.snippets.actions.copy.update');

Route::put('/users/{user}/settings', 'API\UserSettingsController@update')->name('api.users.settings.update');

Route::delete('/cache', function() {
    Cache::flush();
});
