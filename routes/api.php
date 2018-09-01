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
    Route::prefix('product')->group(function () {
        Route::prefix('service')->group(function () {
            Route::get('gencode', 'ProductController@genProductCode');
            Route::get('price/{id}', 'ProductController@getProductPrice');
            Route::get('autoComplete', 'ProductController@autoComplete');
        });
    });
});

//API: Product Category
Route::middleware('auth:api')->namespace('API')->group(function () {
    Route::apiResource('category', 'CategoryController');
});

//API: Warehouse
Route::middleware('auth:api')->namespace('API')->group(function () {
    Route::apiResource('warehouse', 'WarehouseController');
});

//API: Inventory
Route::middleware('auth:api')->namespace('API')->group(function () {
    Route::prefix('inventory')->group(function () {
        //Route::put('quantity/{id}', 'InventoryController@updateQuantity');

        Route::put('quantity/{id}', 'InventoryController@update');
        Route::prefix('quantity')->group(function () {
            Route::get('sum', 'InventoryController@getSumQuantity');
        });
        Route::get('totalprice', 'InventoryController@getTotalPrice');
    });
});

//API: Document
Route::middleware('auth:api')->namespace('API')->group(function () {
    Route::apiResource('document', 'DocumentController');
    Route::prefix('document')->group(function () {
        Route::prefix('service')->group(function () {
            Route::get('gennumber/{type}', 'DocumentController@genDocNumber');
            Route::get('revenue/{type}', 'DocumentController@revenue');
        });
    });
});

// //API: Service
// Route::middleware('auth:api')->namespace('API')->group(function () {
//     Route::prefix('document')->group(function () {
//     });
// });

//API: Inventory Log
// Route::middleware('auth:api')->namespace('API')->group(function () {
//     Route::get('inventoryLog/byDate/{d}/{m}/{y}', 'InventoryLogController@showByDate');
//     Route::apiResource('inventoryLog', 'InventoryLogController');
// });

//API: Report
Route::middleware('auth:api')->namespace('API')->group(function () {
    Route::prefix('report')->group(function () {
        Route::get('all', 'ReportController@index');
    });
});

//Check API
Route::middleware('auth:api')->get('/product/detail/default', function (Request $request) {
    echo 'ok';
});
