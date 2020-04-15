<?php

use Illuminate\Support\Facades\Route;

Route::post('/login', 'API\LoginController@store')->name('api.login.store');
Route::delete('/logout', 'API\LoginController@destroy')->name('api.login.destroy');
Route::post('/register', 'API\RegisterController@store')->name('api.register.store');

Route::get('/snippets', 'API\SnippetsController@index')->name('api.snippets.index');
Route::post('/snippets', 'API\SnippetsController@store')->name('api.snippets.store');
Route::get('/snippets/{snippet}', 'API\SnippetsController@show')->name('api.snippets.show');
Route::put('/snippets/{snippet}', 'API\SnippetsController@update')->name('api.snippets.update');
Route::delete('/snippets/{snippet}', 'API\SnippetsController@destroy')->name('api.snippets.destroy');

Route::post('/tags', 'API\TagsController@store')->name('api.tags.store');
Route::delete('/tags/{tag}', 'API\TagsController@destroy')->name('api.tags.destroy');

Route::post('/snippets/favorite/{snippet}', 'API\FavoriteSnippetsController@store')->name('api.snippets.favorite.store');
Route::delete('/snippets/favorite/{snippet}', 'API\FavoriteSnippetsController@destroy')->name('api.snippets.favorite.destroy');

Route::put('/snippets/actions/copy/{snippet}', 'API\SnippetsActionsCopyController@update')->name('api.snippets.actions.copy.store');
