<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use Illuminate\Http\Request;
use App\Services\TripService;
use Illuminate\Http\JsonResponse;
use Exception;

class TripController extends Controller
{
    protected TripService $tripService;

    public function __construct(TripService $tripService)
    {
        $this->tripService = $tripService;
    }

    /**
     * Iniciar viagem
     */
    public function start(StartTripRequest $request, Trip $trip): JsonResponse
    {
        $this->authorize('start', $trip);

        try {
            $trip = $this->tripService->startTrip(
                $trip,
                $request->integer('initial_odometer')
            );

            return response()->json([
                'message' => 'Viagem iniciada com sucesso.',
                'data' => $trip
            ]);

        } catch (Exception $e) {

            return response()->json([
                'message' => $e->getMessage()
            ], 400);

        }
    }
    /**
     * Finalizar viagem
     */
    public function finish(FinishTripRequest $request, Trip $trip): JsonResponse
    {
        $this->authorize('finish', $trip);

        try {

            $trip = $this->tripService->finishTrip(
                $trip,
                $request->integer('final_odometer')
            );

            return response()->json([
                'message' => 'Viagem finalizada com sucesso.',
                'data' => $trip
            ]);

        } catch (Exception $e) {

            return response()->json([
                'message' => $e->getMessage()
            ], 400);

        }
    }
}