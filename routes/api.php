<?php

use App\Http\Controllers\API\Auth\RegisterController;
use App\Http\Controllers\API\Upload\UploadController;
use App\Models\Upload;
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
        Route::prefix('/uploads')->name('uploads.')->group(function () {
            Route::post('/', [ UploadController::class, 'uploadGif' ])
                ->can('create', Upload::class)
                ->name('upload');
            Route::prefix('/{upload}')->group(function () {
                Route::get('/', [ UploadController::class, 'getUpload' ])
                    ->can('view', 'upload')
                    ->name('getUploadData');
                Route::get('/file', [ UploadController::class, 'getFile' ])
                    ->can('view', 'upload')
                    ->name('getUploadFile');
                Route::put('/meta', [ UploadController::class, 'updateMeta' ])
                    ->can('update', 'upload')
                    ->name('updateMeta');
            });
        });
    });
});
