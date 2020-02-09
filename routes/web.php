<?php

Route::get('/', 'SnippetsController@index')->name('snippets.index');
Route::get('/snippets/create', 'SnippetsController@create')->name('snippets.create');
Route::get('/snippets/{snippet}', 'SnippetsController@show')->name('snippets.show');
Route::post('/snippets', 'SnippetsController@store')->name('snippets.store');

Route::get('/snippets/{snippet}/forks/create', 'SnippetsForksController@create')->name('snippets.forks.create');
Route::post('/snippets/{snippet}/forks/', 'SnippetsForksController@store')->name('snippets.forks.store');

Auth::routes();

Route::get('/{user}/snippets', 'UserSnippetsController@index')->name('user.snippets');
Route::get('/{user}/forked-snippets', 'UserForkedSnippetsController@index')->name('user.forked-snippets');
