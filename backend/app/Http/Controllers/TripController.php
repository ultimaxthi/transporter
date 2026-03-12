<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Enums\TripStatus;
use Illuminate\Http\Request;
use App\Services\TripService;
use App\Http\Requests\StoreTripRequest;
use App\Http\Requests\StartTripRequest;
use App\Http\Requests\FinishTripRequest;
use App\Http\Requests\CancelTripRequest;
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
     * Listar viagens
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Trip::class);

        $query = Trip::query()
            ->with(['driver', 'vehicle', 'operator']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('driver_id')) {
            $query->where('driver_id', $request->driver_id);
        }

        if ($request->filled('operator_id')) {
            $query->where('operator_id', $request->operator_id);
        }

        if ($request->user()->isDriver()) {
            $query->where('driver_id', $request->user()->id);
        }

        $trips = $query->latest()->paginate(15);

        return response()->json($trips);
    }

    /**
     * Criar viagem
     */
    public function store(StoreTripRequest $request): JsonResponse
    {
        $this->authorize('create', Trip::class);

        try {
            $data = $request->validated();
            $trip = $this->tripService->createTrip($data);

            return response()->json([
                'message' => 'Viagem criada com sucesso.',
                'data' => $trip
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Visualizar viagem
     */
    public function show(Trip $trip): JsonResponse
    {
        $this->authorize('view', $trip);

        $trip->load(['driver', 'vehicle', 'operator']);

        return response()->json($trip);
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

    /**
     * Cancelar viagem
     */
    public function cancel(CancelTripRequest $request, Trip $trip): JsonResponse
    {
        $this->authorize('cancel', $trip);

        try {
            $trip = $this->tripService->cancelTrip(
                $trip,
                $request->cancellation_reason
            );

            return response()->json([
                'message' => 'Viagem cancelada com sucesso.',
                'data' => $trip
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }
}