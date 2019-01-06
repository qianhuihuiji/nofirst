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

Route::get('/', function () {
    return view('welcome');
});


Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/threads/create', 'threadsController@create')->name('threads.create');
Route::get('/threads/{channel?}', 'threadsController@index')->name('threads.index');
Route::get('/threads/{channel}/{thread}', 'threadsController@show')->name('threads.show');
Route::post('/threads', 'threadsController@store')->name('threads.store');
Route::get('/threads/{thread}/edit', 'threadsController@edit')->name('threads.edit');
Route::patch('/threads/{thread}', 'threadsController@update')->name('threads.update');
Route::delete('/threads/{thread}', 'threadsController@destroy')->name('threads.destroy');

Route::post('/threads/{channel}/{thread}/replies','RepliesController@store');
