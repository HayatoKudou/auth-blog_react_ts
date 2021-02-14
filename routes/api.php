<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('topData', 'App\Http\Controllers\API\TopController@topData');
Route::post('getAdminData', 'App\Http\Controllers\API\TopController@getAdminData');
Route::post('post', 'App\Http\Controllers\API\TopController@post');
Route::post('article_search', 'App\Http\Controllers\API\TopController@article_search');

Route::post('login', 'App\Http\Controllers\API\AuthController@login');
Route::post('register', 'App\Http\Controllers\API\AuthController@register');
Route::post('contact', 'App\Http\Controllers\API\AuthController@contact');

Route::get('exmple', 'App\Http\Controllers\API\ToolsController@get_api_endPoint');
Route::post('exmple', 'App\Http\Controllers\API\ToolsController@post_api_endPoint');
