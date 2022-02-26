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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('/all/items', 'App\Http\Controllers\ItemController@all')->name('api.item.all');
Route::get('/all/item-details/search', 'App\Http\Controllers\ItemController@detail_search')->name('api.item-detail.search');
Route::post('/requests', 'App\Http\Controllers\RequestItemController@store')->name('api.request.store');
Route::get('/requests', 'App\Http\Controllers\RequestItemController@index')->name('api.request.index');
Route::get('/categories', 'App\Http\Controllers\ItemController@categories')->name('api.request.index');
Route::get('/requests/items/scan', 'App\Http\Controllers\RequestItemController@scan')->name('api.request.scan');
Route::post('/request/{id}/process', 'App\Http\Controllers\RequestItemController@process')->name('api.request.process');
Route::put('/request/{id}', 'App\Http\Controllers\RequestItemController@update')->name('api.request.update');
Route::prefix('items')->group(function () {
    Route::get('/', 'App\Http\Controllers\ItemController@index')->name('api.item.index');
    Route::get('/{id}/request', 'App\Http\Controllers\ItemController@forRequest')->name('api.item.request');
    Route::get('/{id}/details', 'App\Http\Controllers\ItemDetailController@index')->name('api.item.details');
    Route::post('/{id}/barcodes', 'App\Http\Controllers\BarcodeController@store')->name('api.item.barcode.create');
    Route::get('/{id}/barcodes', 'App\Http\Controllers\BarcodeController@index')->name('api.item.barcode.create');
    Route::put('/{itemid}/details/{id}', 'App\Http\Controllers\ItemDetailController@destroy')->name('api.item.details');
    Route::get('/{id}/history', 'App\Http\Controllers\ItemHistoryController@index')->name('api.item.details');
    Route::get('/{id}/stock-months', 'App\Http\Controllers\ItemDetailController@stockMonths')->name('api.item.details.stockMonths');
    Route::post('/', 'App\Http\Controllers\ItemController@store')->name('api.item.store');
    Route::put('/{id}', 'App\Http\Controllers\ItemController@update')->name('api.item.update');
    Route::post('/{id}/serial', 'App\Http\Controllers\ItemController@addSerial')->name('api.item.add.serial');
    Route::delete('/{id}', 'App\Http\Controllers\ItemController@destroy')->name('api.item.destroy');
});

Route::prefix('users')->group(function () {
    Route::get('/', 'App\Http\Controllers\UserController@index')->name('api.user.index');
    Route::post('/', 'App\Http\Controllers\UserController@store')->name('api.user.store');
    Route::put('/{id}', 'App\Http\Controllers\UserController@update')->name('api.user.update');
    Route::delete('/{id}', 'App\Http\Controllers\UserController@destroy')->name('api.user.destroy');
});