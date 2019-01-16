<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'threadsController@index');


Auth::routes();

Route::get('/threads/create', 'threadsController@create')->name('threads.create');
Route::get('/threads/{channel?}', 'threadsController@index')->name('threads.index');
Route::get('/threads/{channel}/{thread}', 'threadsController@show')->name('threads.show');
Route::post('/threads', 'threadsController@store')->name('threads.store');
Route::get('/threads/{thread}/edit', 'threadsController@edit')->name('threads.edit');
Route::patch('/threads/{thread}', 'threadsController@update')->name('threads.update');
Route::delete('/threads/{channel}/{thread}', 'threadsController@destroy')->name('threads.destroy');

Route::get('/threads/{channel}/{thread}/replies','RepliesController@index');
Route::post('/threads/{channel}/{thread}/replies','RepliesController@store');
Route::patch('/replies/{reply}','RepliesController@update');
Route::delete('/replies/{reply}','RepliesController@destroy');

Route::post('/replies/{reply}/favorites','FavoritesController@store');
Route::delete('/replies/{reply}/favorites','FavoritesController@destroy');

Route::post('/threads/{channel}/{thread}/subscriptions','ThreadSubscriptionsController@store');
Route::delete('/threads/{channel}/{thread}/subscriptions','ThreadSubscriptionsController@destroy');

Route::get('/profiles/{user}','ProfilesController@show')->name('profile');

Route::get('/profiles/{user}/notifications','UserNotificationsController@index');
Route::delete('/profiles/{user}/notifications/{notification}','UserNotificationsController@destroy');

Route::get('api/users','Api\UsersController@index');
Route::post('api/users/{user}/avatar','Api\UserAvatarController@store')->middleware('auth')->name('avatar');
