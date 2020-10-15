<?php

use App\Http\Controllers;
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

Route::group([
    'middleware' => 'store.check',
], function () {
    Route::get('shop-config', [Controllers\ShopConfigController::class, 'index']);

    Route::get('categories', [Controllers\CategoriesController::class, 'index']);

    Route::post('authenticated/mini-program', [Controllers\AuthenticatedsController::class, 'miniProgramsStore']);

    Route::group([
        'prefix'     => 'mine',
        'middleware' => 'authenticated.check',
    ], function () {
        Route::post('payments', [Controllers\Mine\PaymentsController::class, 'store']);
    });
});
