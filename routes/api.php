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

//API: Product
Route::middleware('auth:api')->namespace('API')->group(function () {
    Route::apiResource('product', 'ProductController');
    Route::prefix('service')->group(function () {
        Route::prefix('product')->group(function () {
            Route::get('getcode', 'ProductController@getProductCode');
        });
    });
});

//Check API
Route::middleware('auth:api')->get('/product/detail/default', function (Request $request) {
    echo 'ok';
});
