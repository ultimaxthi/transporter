<?php

namespace App\Observers;

use App\Models\VehicleDriver;
use App\Models\User;
use App\Models\Vehicle;
use Exception;

class VehicleDriverObserver
{
    /**
     * Executa antes de criar uma atribuição de motorista
     */
    public function creating(VehicleDriver $assignment): void
    {
        $driver = User::findOrFail($assignment->driver_id);
        $vehicle = Vehicle::findOrFail($assignment->vehicle_id);

        // Verifica se o usuário é um motorista
        if (!$driver->isDriver()) {
            throw new Exception('O usuário não é um motorista.');
        }

        // Verifica se o motorista já tem um veículo ativo
        if ($driver->activeVehicle()->exists()) {
            throw new Exception('O motorista já possui um veículo atribuído.');
        }

        // Verifica se o veículo já está atribuído
        if ($vehicle->activeDriver()->exists()) {
            throw new Exception('O veículo já está atribuído a outro motorista.');
        }

        // Define a data de atribuição se não estiver definida
        if (!$assignment->assigned_at) {
            $assignment->assigned_at = now();
        }

        // Define como ativo por padrão
        $assignment->active = true;
    }

    /**
     * Executa antes de atualizar uma atribuição
     */
    public function updating(VehicleDriver $assignment): void
    {
        // Se estiver desativando a atribuição
        if ($assignment->isDirty('active') && !$assignment->active) {
            $vehicle = Vehicle::findOrFail($assignment->vehicle_id);

            // Não permite desativar se o veículo estiver em viagem
            if ($vehicle->isInTrip()) {
                throw new Exception('Não é possível remover a atribuição de um veículo em viagem.');
            }

            // Define a data de desatribuição
            if (!$assignment->unassigned_at) {
                $assignment->unassigned_at = now();
            }
        }
    }
}