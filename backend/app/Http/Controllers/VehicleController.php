<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVehicleRequest;
use App\Http\Requests\UpdateVehicleRequest;
use App\Models\Vehicle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Listar veículos
    |--------------------------------------------------------------------------
    */

    public function index(Request $request): JsonResponse
    {
        $query = Vehicle::query();

        // filtro por disponibilidade
        if ($request->boolean('available')) {
            $query->available();
        }

        // filtro por marca
        if ($request->filled('brand')) {
            $query->where('brand', $request->brand);
        }

        // filtro por tipo
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $vehicles = $query->paginate(15);

        return response()->json($vehicles);
    }

    /*
    |--------------------------------------------------------------------------
    | Criar veículo
    |--------------------------------------------------------------------------
    */

    public function store(StoreVehicleRequest $request): JsonResponse
    {
        $vehicle = Vehicle::create($request->validated());

        return response()->json($vehicle, 201);
    }

    /*
    |--------------------------------------------------------------------------
    | Mostrar veículo
    |--------------------------------------------------------------------------
    */

    public function show(Vehicle $vehicle): JsonResponse
    {
        $vehicle->load([
            'activeDriver',
            'activeTrip',
            'activeMaintenance'
        ]);

        return response()->json($vehicle);
    }

    /*
    |--------------------------------------------------------------------------
    | Atualizar veículo
    |--------------------------------------------------------------------------
    */

    public function update(UpdateVehicleRequest $request, Vehicle $vehicle): JsonResponse
    {
        $vehicle->update($request->validated());

        return response()->json($vehicle);
    }

    /*
    |--------------------------------------------------------------------------
    | Remover veículo
    |--------------------------------------------------------------------------
    */

    public function destroy(Vehicle $vehicle): JsonResponse
    {
        $vehicle->delete();

        return response()->json(null, 204);
    }
}