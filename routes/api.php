<?php

use App\Http\Controllers\API\Auth\RegisterController;
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
});
