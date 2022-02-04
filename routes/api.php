<?php

use App\Http\Controllers\API\Auth\RegisterController;
use App\Http\Controllers\API\Upload\UploadController;
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

Route::middleware('auth.api-token')->group(function () {
    Route::prefix('/auth')->name('auth.')->group(function () {
        Route::post('register', [ RegisterController::class, 'register' ])->name('register');
    });

    Route::middleware('auth:external-id')->group(function () {
        Route::post('/uploads', [ UploadController::class, 'uploadGif' ])->name('uploadGif');
    });
});
