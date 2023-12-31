<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\ApiAuthMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/users', [UserController::class, 'register']);
Route::post('/users/login', [UserController::class, 'login']);

Route::middleware([ApiAuthMiddleware::class])->group(function () {
    Route::get('/users/current', [UserController::class, 'get']);
    Route::patch('/users/current', [UserController::class, 'update']);

    Route::delete('/users/logout', [UserController::class, 'logout']);


    Route::post('/contacts', [ContactController::class, 'store']);
    Route::get('/contacts', [ContactController::class, 'search']);
    Route::get('/contacts/{id}', [ContactController::class, 'get'])->where('id', '[0-9]+');
    Route::put('/contacts/{id}', [ContactController::class, 'update'])->where('id', '[0-9]+');
    Route::delete('/contacts/{id}', [ContactController::class, 'destory'])->where('id', '[0-9]+');

    Route::post('/contacts/{contactId}/addresses', [AddressController::class, 'store'])->where('contactId', '[0-9]+');
    Route::get('/contacts/{contactId}/addresses', [AddressController::class, 'list'])->where('contactId', '[0-9]+');
    Route::get('/contacts/{contactId}/addresses/{addressId}', [AddressController::class, 'get'])
        ->where('contactId', '[0-9]+')
        ->where('addressId', '[0-9]+');
    Route::put('/contacts/{contactId}/addresses/{addressId}', [AddressController::class, 'update'])
        ->where('contactId', '[0-9]+')
        ->where('addressId', '[0-9]+');
    Route::delete('/contacts/{contactId}/addresses/{addressId}', [AddressController::class, 'destroy'])
        ->where('contactId', '[0-9]+')
        ->where('addressId', '[0-9]+');
});


// Route::post('/upload', [PostController::class, 'upload']);


Route::get('/files', [FileController::class, 'list']);
Route::post('/files', [FileController::class, 'store']);
Route::get('/files/{id}', [FileController::class, 'get'])->where('id', '[0-9]+');
// Route::post('/files/{id}', [FileController::class, 'update'])->where('id', '[0-9]+');
Route::put('/files/{id}', [FileController::class, 'update'])->where('id', '[0-9]+');
Route::delete('/files/{id}', [FileController::class, 'destroy'])->where('id', '[0-9]+');
Route::get('/files/{id}/download', [FileController::class, 'download'])->where('id', '[0-9]+');
