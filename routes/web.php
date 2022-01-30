<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::get('/items', [PageController::class, 'items'])->middleware(['auth'])->name('items');
Route::get('/requests', [PageController::class, 'requests'])->middleware(['auth'])->name('requests');
Route::get('/create-request', [PageController::class, 'create_request'])->middleware(['auth'])->name('create-request');
Route::get('/requests/{id}', [PageController::class, 'created_request'])->middleware(['auth'])->name('created-request');
Route::get('/requests/{id}/process', [PageController::class, 'request_process'])->middleware(['auth'])->name('process-request');

require __DIR__.'/auth.php';

Auth::routes();

Route::get('/home', [PageController::class, 'items'])->middleware(['auth'])->name('items');
Route::get('/', [PageController::class, 'items'])->middleware(['auth'])->name('items');
