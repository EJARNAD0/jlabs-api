<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HistoryController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // history endpoints
    Route::get('/histories', [HistoryController::class, 'index']);
    Route::post('/histories', [HistoryController::class, 'store']);
    Route::delete('/histories', [HistoryController::class, 'bulkDelete']);
});
