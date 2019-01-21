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
Route::get('/threads/{channel?}', 'threadsController@index')->name('threads');
Route::get('/threads/{channel}/{thread}', 'threadsController@show')->name('threads.show');
Route::post('/threads', 'threadsController@store')->name('threads.store')->middleware('must-be-confirmed');
Route::get('/threads/{thread}/edit', 'threadsController@edit')->name('threads.edit');
Route::delete('/threads/{channel}/{thread}', 'threadsController@destroy')->name('threads.destroy');

Route::post('locked-threads/{thread}','LockedThreadsController@store')->name('locked-threads.store')->middleware('admin');

Route::get('/threads/{channel}/{thread}/replies','RepliesController@index');
Route::post('/threads/{channel}/{thread}/replies','RepliesController@store');
Route::patch('/replies/{reply}','RepliesController@update');
Route::delete('/replies/{reply}','RepliesController@destroy')->name('replies.destroy');

Route::post('/replies/{reply}/best','BestRepliesController@store')->name('best-replies.store');

Route::post('/replies/{reply}/favorites','FavoritesController@store');
Route::delete('/replies/{reply}/favorites','FavoritesController@destroy');

Route::post('/threads/{channel}/{thread}/subscriptions','ThreadSubscriptionsController@store');
Route::delete('/threads/{channel}/{thread}/subscriptions','ThreadSubscriptionsController@destroy');

Route::get('/profiles/{user}','ProfilesController@show')->name('profile');

Route::get('/profiles/{user}/notifications','UserNotificationsController@index');
Route::delete('/profiles/{user}/notifications/{notification}','UserNotificationsController@destroy');

Route::get('/register/confirm','Auth\RegisterConfirmationController@index')->name('register.confirm');

Route::get('api/users','Api\UsersController@index');
Route::post('api/users/{user}/avatar','Api\UserAvatarController@store')->middleware('auth')->name('avatar');
