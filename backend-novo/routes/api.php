<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TripController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VehicleController;

/*
|--------------------------------------------------------------------------
| Rotas públicas
|--------------------------------------------------------------------------
*/
Route::post('/login', [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| Rotas protegidas por autenticação
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum'])->group(function () {

    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Usuários
    Route::apiResource('users', UserController::class);

    // Veículos
    Route::apiResource('vehicles', VehicleController::class);

    // Viagens
    Route::apiResource('trips', TripController::class)->only([
        'index', 'store', 'show'
    ]);

    Route::prefix('trips')->group(function () {
        Route::post('/{trip}/start',  [TripController::class, 'start']);
        Route::post('/{trip}/finish', [TripController::class, 'finish']);
        Route::post('/{trip}/cancel', [TripController::class, 'cancel']);
    });

});