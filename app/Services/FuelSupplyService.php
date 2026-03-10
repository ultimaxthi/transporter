<?php

namespace App\Services;

use App\Models\FuelSupply;
use App\Models\Vehicle;
use Illuminate\Support\Facades\DB;
use Exception;

class FuelSupplyService
{
    public function registerSupply(array $data): FuelSupply
    {
        return DB::transaction(function () use ($data) {

            $vehicle = Vehicle::lockForUpdate()->findOrFail($data['vehicle_id']);

            $odometer = (int) $data['odometer'];

            /*
            |--------------------------------------------------------------------------
            | Validação de odômetro
            |--------------------------------------------------------------------------
            */

            if ($odometer < $vehicle->current_odometer) {
                throw new Exception('O odômetro informado é menor que o atual do veículo.');
            }

            /*
            |--------------------------------------------------------------------------
            | Validação de litros
            |--------------------------------------------------------------------------
            */

            if ($data['liters'] <= 0) {
                throw new Exception('A quantidade de litros deve ser maior que zero.');
            }

            /*
            |--------------------------------------------------------------------------
            | Calcular custo total
            |--------------------------------------------------------------------------
            */

            $totalCost = round(
                $data['liters'] * $data['price_per_liter'],
                2
            );

            /*
            |--------------------------------------------------------------------------
            | Criar abastecimento
            |--------------------------------------------------------------------------
            */

            $fuelSupply = FuelSupply::create([
                'vehicle_id' => $vehicle->id,
                'driver_id' => $data['driver_id'],
                'liters' => $data['liters'],
                'price_per_liter' => $data['price_per_liter'],
                'total_cost' => $totalCost,
                'odometer' => $odometer,
                'supplied_at' => $data['supplied_at'] ?? now(),
            ]);

            /*
            |--------------------------------------------------------------------------
            | Atualizar odômetro do veículo (opcional)
            |--------------------------------------------------------------------------
            */

            if ($odometer > $vehicle->current_odometer) {
                $vehicle->update([
                    'current_odometer' => $odometer
                ]);
            }

            return $fuelSupply->refresh();
        });
    }
}