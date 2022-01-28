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
Route::prefix('items')->group(function () {
    Route::get('/', 'App\Http\Controllers\ItemController@index')->name('api.item.index');
    Route::get('/{id}/details', 'App\Http\Controllers\ItemDetailController@index')->name('api.item.details');
    Route::get('/{id}/history', 'App\Http\Controllers\ItemHistoryController@index')->name('api.item.details');
    Route::post('/', 'App\Http\Controllers\ItemController@store')->name('api.item.store');
    Route::put('/{id}', 'App\Http\Controllers\ItemController@update')->name('api.item.update');
    Route::post('/{id}/serial', 'App\Http\Controllers\ItemController@addSerial')->name('api.item.add.serial');
    Route::delete('/{id}', 'App\Http\Controllers\ItemController@destroy')->name('api.item.destroy');
});