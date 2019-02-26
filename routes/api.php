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

/* Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
}); */

Route::middleware('apiToken')->group(function() {
    Route::post('posts', 'PostController@store');
    Route::post('posts/{id}', 'PostController@update');
    Route::delete('posts/{id}', 'PostController@destroy');
});

Route::get('posts', 'PostController@index');
Route::get('posts/{id}', 'PostController@show');

Route::post('auth', 'AuthController@postLogin');
Route::post('log', 'AuthController@postRegister');