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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::any('login', 'ApiController@userLogin');
Route::any('register', 'ApiController@registerUser');
Route::any('get-home-products', 'ApiController@getHomeProducts');
Route::any('get-promotion-products', 'ApiController@getPromotionProducts');
