<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFuelSupplyRequest;
use App\Models\FuelSupply;
use App\Models\Vehicle;
use App\Services\FuelSupplyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

class FuelSupplyController extends Controller
{
    protected FuelSupplyService $fuelSupplyService;

    public function __construct(FuelSupplyService $fuelSupplyService)
    {
        $this->fuelSupplyService = $fuelSupplyService;
    }

    /**
     * Listar abastecimentos
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', FuelSupply::class);

        $query = FuelSupply::query()
            ->with(['vehicle', 'driver']);

        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        if ($request->filled('driver_id')) {
            $query->where('driver_id', $request->driver_id);
        }

        $supplies = $query->orderByDesc('supplied_at')->paginate(15);

        return response()->json($supplies);
    }

    /**
     * Registrar novo abastecimento
     */
    public function store(StoreFuelSupplyRequest $request): JsonResponse
    {
        $this->authorize('create', FuelSupply::class);

        try {
            $supply = $this->fuelSupplyService->registerSupply($request->validated());

            return response()->json([
                'message' => 'Abastecimento registrado com sucesso.',
                'data' => $supply
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Exibir abastecimento
     */
    public function show(FuelSupply $supply): JsonResponse
    {
        $this->authorize('view', $supply);

        $supply->load(['vehicle', 'driver']);

        return response()->json($supply);
    }

    /**
     * Obter métricas de consumo para um veículo
     */
    public function vehicleMetrics(Vehicle $vehicle): JsonResponse
    {
        $this->authorize('viewAny', FuelSupply::class);

        $lastSupplies = $vehicle->fuelSupplies()->recent()->take(10)->get();

        $metrics = [
            'average_consumption' => $vehicle->averageConsumption(),
            'total_liters' => $vehicle->fuelSupplies()->sum('liters'),
            'total_cost' => $vehicle->fuelSupplies()->sum('total_cost'),
            'last_supplies' => $lastSupplies->map(function ($supply) {
                return [
                    'id' => $supply->id,
                    'date' => $supply->supplied_at->format('d/m/Y'),
                    'liters' => $supply->liters,
                    'cost' => $supply->total_cost,
                    'odometer' => $supply->odometer,
                    'consumption' => $supply->consumptionKmPerLiter(),
                ];
            }),
        ];

        return response()->json($metrics);
    }
}