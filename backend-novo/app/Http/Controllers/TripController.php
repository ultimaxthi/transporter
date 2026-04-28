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
        $query = Trip::query()
            ->with(['driver', 'vehicle', 'operator']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('driver_id')) {
            $query->where('driver_id', $request->driver_id);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Motorista só vê as próprias viagens
        if (auth()->user()->role === 'driver') {
            $query->where('driver_id', auth()->id());
        }

        $trips = $query->latest()->paginate(15);

        return response()->json($trips);
    }

    /**
     * Criar viagem
     */
    public function store(StoreTripRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $data['operator_id'] = auth()->id();
            $data['status'] = TripStatus::Pending->value;

            $trip = $this->tripService->createTrip($data);

            return response()->json([
                'message' => 'Viagem criada com sucesso.',
                'data'    => $trip
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
        $trip->load(['driver', 'vehicle', 'operator']);

        return response()->json($trip);
    }

    /**
     * Iniciar viagem
     */
    public function start(StartTripRequest $request, Trip $trip): JsonResponse
    {
        try {
            $trip = $this->tripService->startTrip(
                $trip,
                $request->integer('initial_odometer')
            );

            return response()->json([
                'message' => 'Viagem iniciada com sucesso.',
                'data'    => $trip
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
        try {
            $trip = $this->tripService->finishTrip(
                $trip,
                $request->integer('final_odometer')
            );

            return response()->json([
                'message' => 'Viagem finalizada com sucesso.',
                'data'    => $trip
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
    public function cancel(Request $request, Trip $trip): JsonResponse
    {
        try {
            $trip->update([
                'status'              => TripStatus::Cancelled->value,
                'cancelled_at'        => now(),
                'cancellation_reason' => $request->cancellation_reason,
            ]);

            return response()->json([
                'message' => 'Viagem cancelada com sucesso.',
                'data'    => $trip
            ]);

        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }
}