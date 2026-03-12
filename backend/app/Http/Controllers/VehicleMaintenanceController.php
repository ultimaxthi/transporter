<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVehicleMaintenanceRequest;
use App\Http\Requests\FinishVehicleMaintenanceRequest;
use App\Models\Vehicle;
use App\Models\VehicleMaintenance;
use App\Services\VehicleMaintenanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

class VehicleMaintenanceController extends Controller
{
    protected VehicleMaintenanceService $maintenanceService;

    public function __construct(VehicleMaintenanceService $maintenanceService)
    {
        $this->maintenanceService = $maintenanceService;
    }

    /**
     * Listar manutenções
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', VehicleMaintenance::class);

        $query = VehicleMaintenance::query()
            ->with(['vehicle', 'createdBy']);

        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        if ($request->filled('active')) {
            if ($request->boolean('active')) {
                $query->active();
            } else {
                $query->finished();
            }
        }

        $maintenances = $query->orderByDesc('start_date')->paginate(15);

        return response()->json($maintenances);
    }

    /**
     * Registrar nova manutenção
     */
    public function store(StoreVehicleMaintenanceRequest $request): JsonResponse
    {
        $this->authorize('create', VehicleMaintenance::class);

        try {
            $data = $request->validated();
            $data['created_by'] = auth()->id();

            $maintenance = $this->maintenanceService->startMaintenance($data);

            return response()->json([
                'message' => 'Manutenção registrada com sucesso.',
                'data' => $maintenance
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Exibir manutenção
     */
    public function show(VehicleMaintenance $maintenance): JsonResponse
    {
        $this->authorize('view', $maintenance);

        $maintenance->load(['vehicle', 'createdBy']);

        return response()->json($maintenance);
    }

    /**
     * Finalizar manutenção
     */
    public function finish(FinishVehicleMaintenanceRequest $request, VehicleMaintenance $maintenance): JsonResponse
    {
        $this->authorize('finish', $maintenance);

        try {
            $maintenance = $this->maintenanceService->finishMaintenance(
                $maintenance,
                $request->validated()
            );

            return response()->json([
                'message' => 'Manutenção finalizada com sucesso.',
                'data' => $maintenance
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Verificar alertas de manutenção preventiva
     */
    public function checkPreventiveAlerts(Vehicle $vehicle): JsonResponse
    {
        $this->authorize('viewAny', VehicleMaintenance::class);

        $alerts = $this->maintenanceService->checkPreventiveMaintenance($vehicle);

        return response()->json($alerts);
    }
}