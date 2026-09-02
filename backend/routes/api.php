<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CaptchaController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\StarController;
use App\Http\Controllers\Api\TerbilangController;
use App\Http\Controllers\Api\InputDataController;
use App\Http\Controllers\Api\HistoryController;

Route::get('/health', function () {
    return response()->json([
        'success' => true,
        'message' => 'API is running',
    ]);
});

Route::get('/captcha', [CaptchaController::class, 'generate']);

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/me', [AuthController::class, 'me']);

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::post(
        '/stars/generate',
        [StarController::class, 'generate']
    );

    Route::get(
        '/stars/history',
        [StarController::class, 'history']
    );
    
    Route::post(
        '/terbilang/generate',
        [TerbilangController::class, 'generate']
    );

    Route::get(
        '/terbilang/history',
        [TerbilangController::class, 'history']
    );

    Route::post(
        '/input-data',
        [InputDataController::class, 'store']
    );

    Route::get(
        '/input-data',
        [InputDataController::class, 'index']
    );

    Route::get(
        '/history',
        [HistoryController::class, 'index']
    );
});