<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssignVehicleToDriverRequest;
use App\Models\Vehicle;
use App\Models\User;
use App\Services\VehicleService;
use Illuminate\Http\JsonResponse;
use Exception;

class VehicleDriverController extends Controller
{
    protected VehicleService $vehicleService;

    public function __construct(VehicleService $vehicleService)
    {
        $this->vehicleService = $vehicleService;
    }

    /**
     * Atribuir veículo a um motorista
     */
    public function assign(AssignVehicleToDriverRequest $request, Vehicle $vehicle): JsonResponse
    {
        $this->authorize('assignDriver', $vehicle);

        try {
            $driver = User::findOrFail($request->driver_id);

            $assignment = $this->vehicleService->assignToDriver($vehicle, $driver);

            return response()->json([
                'message' => 'Veículo atribuído ao motorista com sucesso.',
                'data' => $assignment
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Remover atribuição de veículo
     */
    public function unassign(Vehicle $vehicle): JsonResponse
    {
        $this->authorize('assignDriver', $vehicle);

        try {
            $this->vehicleService->unassignFromDriver($vehicle);

            return response()->json([
                'message' => 'Atribuição de veículo removida com sucesso.'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Listar histórico de atribuições de um veículo
     */
    public function history(Vehicle $vehicle): JsonResponse
    {
        $assignments = $vehicle->drivers()
            ->withPivot(['assigned_at', 'unassigned_at', 'active'])
            ->orderByDesc('pivot_assigned_at')
            ->get();

        return response()->json($assignments);
    }
}