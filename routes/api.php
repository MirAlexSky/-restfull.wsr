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
    //posts
    Route::post('posts', 'PostController@store');
    Route::post('posts/{id}', 'PostController@update');
    Route::delete('posts/{id}', 'PostController@destroy');

    //comment
    Route::delete('posts/{post_id}/comments/{comment_id}', 'PostController@deleteComment');
});

//posts
Route::get('posts', 'PostController@index');
Route::get('posts/{id}', 'PostController@show');

//comment
Route::post('posts/{post_id}/comments', 'PostController@comment');

//tags
Route::get('posts/tag/{tag_name}', 'PostController@tag');

//auth
Route::post('auth', 'AuthController@postLogin');
Route::post('log', 'AuthController@postRegister');