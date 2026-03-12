<?php

namespace App\Observers;

use App\Models\VehicleMaintenance;
use App\Models\Vehicle;
use Exception;

class VehicleMaintenanceObserver
{
    /**
     * Executa antes de criar uma manutenção
     */
    public function creating(VehicleMaintenance $maintenance): void
    {
        $vehicle = Vehicle::findOrFail($maintenance->vehicle_id);

        // Se não informou o odômetro, usa o atual do veículo
        if (!$maintenance->odometer) {
            $maintenance->odometer = $vehicle->current_odometer;
        }

        // Valida se o veículo está em viagem
        if ($vehicle->isInTrip()) {
            throw new Exception('Não é possível iniciar manutenção em um veículo em viagem.');
        }
    }

    /**
     * Executa antes de atualizar uma manutenção
     */
    public function updating(VehicleMaintenance $maintenance): void
    {
        // Verifica se está finalizando a manutenção
        if ($maintenance->isDirty('end_date') && $maintenance->end_date) {
            // Validar que end_date é posterior a start_date
            if ($maintenance->end_date < $maintenance->start_date) {
                throw new Exception('A data de término deve ser posterior à data de início.');
            }
        }
    }
}