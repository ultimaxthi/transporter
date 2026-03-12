<?php

namespace App\Services;

use App\Models\Vehicle;
use App\Models\VehicleMaintenance;
use App\Enums\MaintenanceType;
use Illuminate\Support\Facades\DB;
use Exception;

class VehicleMaintenanceService
{
    /**
     * Registra uma nova manutenção para um veículo
     */
    public function startMaintenance(array $data): VehicleMaintenance
    {
        return DB::transaction(function () use ($data) {
            $vehicle = Vehicle::lockForUpdate()->findOrFail($data['vehicle_id']);

            if ($vehicle->isInTrip()) {
                throw new Exception('Veículo está em viagem e não pode entrar em manutenção.');
            }

            if ($vehicle->isUnderMaintenance()) {
                throw new Exception('Veículo já está em manutenção.');
            }

            return VehicleMaintenance::create([
                'vehicle_id' => $vehicle->id,
                'type' => $data['type'],
                'description' => $data['description'],
                'odometer' => $data['odometer'] ?? $vehicle->current_odometer,
                'cost' => $data['cost'] ?? 0,
                'start_date' => $data['start_date'] ?? now(),
                'created_by' => $data['created_by'],
            ]);
        });
    }

    /**
     * Finaliza uma manutenção existente
     */
    public function finishMaintenance(VehicleMaintenance $maintenance, array $data): VehicleMaintenance
    {
        return DB::transaction(function () use ($maintenance, $data) {
            if (!$maintenance->isActive()) {
                throw new Exception('Esta manutenção já foi finalizada.');
            }

            $maintenance->update([
                'end_date' => $data['end_date'] ?? now(),
                'cost' => $data['cost'] ?? $maintenance->cost,
                'description' => $data['description'] ?? $maintenance->description,
            ]);

            return $maintenance->refresh();
        });
    }

    /**
     * Obtém o histórico de manutenções de um veículo
     */
    public function getMaintenanceHistory(Vehicle $vehicle)
    {
        return $vehicle->maintenances()
            ->orderByDesc('start_date')
            ->get();
    }

    /**
     * Verifica manutenções preventivas necessárias
     */
    public function checkPreventiveMaintenance(Vehicle $vehicle): array
    {
        $alerts = [];
        $lastOilChange = $vehicle->maintenances()
            ->where('type', MaintenanceType::OilChange)
            ->orderByDesc('start_date')
            ->first();

        // Alerta para troca de óleo (a cada 10.000 km ou 6 meses)
        if (!$lastOilChange || 
            $vehicle->current_odometer - $lastOilChange->odometer > 10000 ||
            now()->diffInMonths($lastOilChange->start_date) > 6) {
            $alerts[] = [
                'type' => MaintenanceType::OilChange->value,
                'message' => 'Veículo necessita de troca de óleo',
                'priority' => 'high'
            ];
        }

        // Outros alertas de manutenção preventiva podem ser adicionados aqui

        return $alerts;
    }
}