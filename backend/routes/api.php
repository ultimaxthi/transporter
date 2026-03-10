<?php

use App\Http\Controllers\VehicleController;     
use App\Http\Controllers\TripController;

use Illuminate\Support\Facades\Route;

Route::get('/test', function () {
    return response()->json([
        'message' => 'API funcionando'
    ]);
});

Route::apiResource('/vehicles', VehicleController::class);

Route::apiResource('trips', TripController::class)
    ->only(['index','show','store','update','destroy']);

Route::prefix('trips')->group(function () {

    Route::post('{trip}/start', [TripController::class, 'start']);

    Route::post('{trip}/finish', [TripController::class, 'finish']);

});