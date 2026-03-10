<?php

namespace App\Observers;

use App\Models\FuelSupply;
use App\Models\Vehicle;
use Illuminate\Support\Facades\DB;
use Exception;

class FuelSupplyObserver
{
    /**
     * Executa antes de salvar no banco
     */
    public function creating(FuelSupply $fuelSupply): void
    {
        $vehicle = Vehicle::lockForUpdate()->findOrFail($fuelSupply->vehicle_id);

        /*
        |--------------------------------------------------------------------------
        | Validar odômetro
        |--------------------------------------------------------------------------
        */

        if ($fuelSupply->odometer < $vehicle->current_odometer) {
            throw new Exception('O odômetro informado é menor que o atual do veículo.');
        }

        /*
        |--------------------------------------------------------------------------
        | Calcular custo total automaticamente
        |--------------------------------------------------------------------------
        */

        $fuelSupply->total_cost = round(
            $fuelSupply->liters * $fuelSupply->price_per_liter,
            2
        );

        /*
        |--------------------------------------------------------------------------
        | Atualizar odômetro do veículo
        |--------------------------------------------------------------------------
        */

        if ($fuelSupply->odometer > $vehicle->current_odometer) {
            $vehicle->update([
                'current_odometer' => $fuelSupply->odometer
            ]);
        }
    }
}