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

Route::post('restaurantList', 'ApiController@restaurantList')->middleware('auth:api');
Route::post('searchRestaurant', 'ApiController@restaurantList')->middleware('auth:api');
Route::post('sortRestaurants', 'ApiController@sortRestaurants')->middleware('auth:api');
Route::post('searchRestaurant', 'ApiController@searchRestaurant')->middleware('auth:api');
