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

Route::post('login', 'API\UserController@login')->name('login');
Route::post('register', 'API\UserController@register')->name('register');

Route::group(['middleware' => 'auth:api'], function(){
    Route::get('cartList', 'API\UserController@cartList');
    Route::post('storeCart', 'API\UserController@storeCart');
    Route::get('deleteCart/{item_id}', 'API\UserController@deleteCart');
    
    Route::get('transactionList', 'API\UserController@transactionList');

    Route::get('itemList', 'API\UserController@itemList');
    Route::get('itemDetail/{item_id}', 'API\UserController@itemDetail');
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
