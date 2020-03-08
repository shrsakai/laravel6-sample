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

Route::group(['namespace' => 'Api', 'prefix' => 'api'], function () {
    Route::group(['middleware' => 'auth'], function () {
        // REST API tasks
        Route::apiResource('tasks', 'TaskController');
        // POST - /tasks/bulk_done_status
        Route::post('tasks/bulk_done_status', 'TaskController@bulkDoneStatus');
    });
});
