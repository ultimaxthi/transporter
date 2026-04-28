<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Enums\VehicleStatus;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\StoreVehicleRequest;
use App\Http\Requests\UpdateVehicleRequest;

class VehicleController extends Controller
{
    public function index(): JsonResponse
    {
        $vehicles = Vehicle::with('activeDriver')
            ->latest()
            ->paginate(15);

        return response()->json($vehicles);
    }

    public function store(StoreVehicleRequest $request): JsonResponse
    {
        $vehicle = Vehicle::create($request->validated());

        return response()->json([
            'message' => 'Veículo criado com sucesso.',
            'data'    => $vehicle
        ], 201);
    }

    public function show(Vehicle $vehicle): JsonResponse
    {
        $vehicle->load(['activeDriver', 'trips', 'maintenances', 'fuelSupplies']);

        return response()->json($vehicle);
    }

    public function update(UpdateVehicleRequest $request, Vehicle $vehicle): JsonResponse
    {
        $vehicle->update($request->validated());

        return response()->json([
            'message' => 'Veículo atualizado com sucesso.',
            'data'    => $vehicle->fresh()
        ]);
    }

    public function destroy(Vehicle $vehicle): JsonResponse
    {
        $vehicle->delete();

        return response()->json([
            'message' => 'Veículo removido com sucesso.'
        ]);
    }
}