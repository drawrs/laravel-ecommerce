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
Route::any('get-categories', 'ApiController@getCategories');

Route::any('get-product', 'ApiController@getProduct');
Route::any('get-profile', 'ApiController@getUserProfile');
Route::any('update-profile', 'ApiController@updateUserProfile');
Route::any('update-shipping-address', 'ApiController@updateShippingAddress');
Route::any('search-products', 'ApiController@searchProduct');
Route::any('get-shopping-carts', 'ApiController@getShoppingCarts');

Route::any('update-cart-item-qty', 'ApiController@updateCartItemQty');
Route::any('insert-cart-item', 'ApiController@insertShoppingCart');
Route::any('delete-cart-item', 'ApiController@deleteCartItem');

Route::any('insert-order', 'ApiController@insertOrder');
Route::any('get-orders', 'ApiController@getOrders');
