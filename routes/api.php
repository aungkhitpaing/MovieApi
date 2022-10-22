<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v1')->group(function () {

    Route::post('login', '\Movie\Api\Auth\Controllers\LoginController@login');
    Route::get('movies', '\Movie\Api\Movie\Controllers\MovieController@index');
    Route::get('movies/{id}', '\Movie\Api\Movie\Controllers\MovieController@show');
    Route::resource('comments','\Movie\Api\Comment\Controllers\CommentController');

});


Route::prefix('v1')->middleware('auth:api')->group( function () {

    Route::post('movies/{id}/upload', '\Movie\Api\Movie\Controllers\MovieController@uploadImage');
    Route::post('movies', '\Movie\Api\Movie\Controllers\MovieController@store');
    Route::put('movies/{id}', '\Movie\Api\Movie\Controllers\MovieController@update');
    Route::delete('movies/{id}', '\Movie\Api\Movie\Controllers\MovieController@destroy');

});



