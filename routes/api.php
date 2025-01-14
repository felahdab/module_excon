<?php

use Illuminate\Support\Facades\Route;
use Modules\Excon\Http\Controllers\PositionController;

/*
 *--------------------------------------------------------------------------
 * API Routes
 *--------------------------------------------------------------------------
 *
 * Here is where you can register API routes for your application. These
 * routes are loaded by the RouteServiceProvider within a group which
 * is assigned the "api" middleware group. Enjoy building your API!
 *
*/

Route::middleware(['forcejson', 'auth:sanctum'])->prefix('v1')->group(function () {
    Route::put("position", [PositionController::class, "store"])->name("position.store");
    #Route::apiResource('excon', ExconController::class)->names('excon');
});
