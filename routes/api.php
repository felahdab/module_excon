<?php

use Illuminate\Support\Facades\Route;
use Modules\Excon\Http\Controllers\PositionController;
use Modules\Excon\Http\Controllers\EngagementController;

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

$middlewares = config("app.debug") ? ["forcejson"] : ['forcejson', 'auth:sanctum'];

Route::middleware($middlewares)->prefix('v1')->group(function () {
    Route::put("position", [PositionController::class, "store"])->name("position.store");
    Route::get("engagements", [EngagementController::class, "index"])->name("engagement.index");

    #Route::apiResource('excon', ExconController::class)->names('excon');
});
