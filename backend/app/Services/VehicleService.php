<?php

namespace App\Services;

use App\Models\Vehicle;
use App\Models\User;
use App\Models\VehicleDriver;
use Illuminate\Support\Facades\DB;
use Exception;

class VehicleService
{
    /**
     * Registra um novo veículo no sistema
     */
    public function registerVehicle(array $data): Vehicle
    {
        return DB::transaction(function () use ($data) {
            return Vehicle::create([
                'plate' => strtoupper($data['plate']),
                'brand' => $data['brand'],
                'model' => $data['model'],
                'year' => $data['year'],
                'type' => $data['type'],
                'patrimony_number' => $data['patrimony_number'] ?? null,
                'current_odometer' => $data['current_odometer'] ?? 0,
            ]);
        });
    }

    /**
     * Atualiza informações do veículo
     */
    public function updateVehicle(Vehicle $vehicle, array $data): Vehicle
    {
        return DB::transaction(function () use ($vehicle, $data) {
            $vehicle->update($data);
            return $vehicle->refresh();
        });
    }

    /**
     * Atribui um veículo a um motorista
     */
    public function assignToDriver(Vehicle $vehicle, User $driver): VehicleDriver
    {
        return DB::transaction(function () use ($vehicle, $driver) {
            if (!$driver->isDriver()) {
                throw new Exception('Usuário não é um motorista.');
            }

            // Verifica se o motorista já tem um veículo ativo
            if ($driver->activeVehicle()->exists()) {
                throw new Exception('Motorista já possui um veículo atribuído.');
            }

            // Verifica se o veículo já está atribuído a outro motorista
            if ($vehicle->isAssigned()) {
                throw new Exception('Veículo já está atribuído a outro motorista.');
            }

            // Desativa qualquer atribuição anterior (segurança)
            VehicleDriver::where('vehicle_id', $vehicle->id)
                ->where('active', true)
                ->update(['active' => false, 'unassigned_at' => now()]);

            // Cria nova atribuição
            return VehicleDriver::create([
                'vehicle_id' => $vehicle->id,
                'driver_id' => $driver->id,
                'assigned_at' => now(),
                'active' => true,
            ]);
        });
    }

    /**
     * Remove a atribuição de um veículo a um motorista
     */
    public function unassignFromDriver(Vehicle $vehicle): bool
    {
        return DB::transaction(function () use ($vehicle) {
            $assignment = $vehicle->activeDriver()->first();

            if (!$assignment) {
                throw new Exception('Veículo não está atribuído a nenhum motorista.');
            }

            if ($vehicle->isInTrip()) {
                throw new Exception('Não é possível remover a atribuição de um veículo em viagem.');
            }

            return $assignment->update([
                'active' => false,
                'unassigned_at' => now()
            ]);
        });
    }

    /**
     * Lista veículos disponíveis para viagens
     */
    public function getAvailableVehicles()
    {
        return Vehicle::available()->get();
    }
}