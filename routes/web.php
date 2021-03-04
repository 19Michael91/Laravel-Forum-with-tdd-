<?php

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/threads', 'ThreadsController@index')->name('threads.index');
Route::get('/threads/create', 'ThreadsController@create')->name('threads.create');
Route::get('/threads/{channel}', 'ThreadsController@index')->name('threads.channel.index');
Route::get('/threads/{channel}/{thread}', 'ThreadsController@show')->name('threads.show');
Route::patch('/threads/{channel}/{thread}', 'ThreadsController@update')->name('threads.update');
Route::delete('/threads/{channel}/{thread}', 'ThreadsController@destroy')->name('threads.delete');
Route::post('/threads', 'ThreadsController@store')->name('threads.store')->middleware('must-be-confirmed');

Route::post('locked-threads/{thread}', 'LockedThreadsController@store')->name('locked-threads.store')->middleware('admin');
Route::delete('locked-threads/{thread}', 'LockedThreadsController@destroy')->name('locked-threads.delete')->middleware('admin');

Route::get('/threads/{channel}/{thread}/replies', 'RepliesController@index')->name('threads.replies.index');
Route::post('/threads/{channel}/{thread}/replies', 'RepliesController@store')->name('threads.replies.store');
Route::delete('/replies/{reply}', 'RepliesController@destroy')->name('replies.delete');
Route::patch('/replies/{reply}', 'RepliesController@update')->name('replies.update');

Route::post('/replies/{reply}/best', 'BestRepliesController@store')->name('best-replies.store');

Route::post('/replies/{reply}/favorites', 'FavoritesController@store')->name('replies.favorites.store');
Route::delete('/replies/{reply}/favorites', 'FavoritesController@destroy')->name('replies.favorites.delete');

Route::post('/threads/{channel}/{thread}/subscriptions', 'ThreadSubscriptionsController@store')->name('thread.subscription.store');
Route::delete('/threads/{channel}/{thread}/subscriptions', 'ThreadSubscriptionsController@destroy')->name('thread.subscription.delete');

Route::get('/profiles/{user}', 'ProfilesController@show')->name('profiles.show');
Route::get('/profiles/{user}/notifications', 'UserNotificationsController@index')->name('profiles.notifications.index');
Route::delete('/profiles/{user}/notifications/{notification}', 'UserNotificationsController@destroy')->name('profiles.notifications.delete');

Route::post('/users/{user}/avatar', 'UserAvatarController@store')->name('user.avatar.store');

Route::get('/register/confirm', 'Auth\RegisterConfirmationController@index')->name('register.confirm');

