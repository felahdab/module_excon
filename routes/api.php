<?php

use Illuminate\Support\Facades\Route;
use Modules\Excon\Http\Controllers\PositionController;
use Modules\Excon\Http\Controllers\EngagementController;
use Modules\Excon\Http\Controllers\PduController;


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

$middlewares = config("app.debug") ? ["forcejson", "auth:sanctum"] : ['forcejson', 'auth:sanctum'];

Route::middleware($middlewares)
->withoutMiddleware(['throttle:api'])
->prefix('v1')
->group(function () {
    Route::put("position", [PositionController::class, "store"])->name("position.store");
    Route::get("engagements", [EngagementController::class, "index"])->name("engagement.index");
    Route::post("ackowledge_engagement", [EngagementController::class, "acknowledge"])->name("engagement.acknowledge");
    Route::post("newpdu", [PduController::class, "store"])->name("pdu.store");

    #Route::apiResource('excon', ExconController::class)->names('excon');
});
