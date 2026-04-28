<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class VehicleSeeder extends Seeder
{
    public function run(): void
    {
        $vehicles = [
            [
                'plate'            => 'ABC-1234',
                'brand'            => 'Fiat',
                'model'            => 'Doblò',
                'year'             => 2022,
                'type'             => 'van',
                'patrimony_number' => 'PAT-001',
                'current_odometer' => 15000,
                'status'           => 'available',
            ],
            [
                'plate'            => 'DEF-5678',
                'brand'            => 'Mercedes',
                'model'            => 'Sprinter',
                'year'             => 2021,
                'type'             => 'van',
                'patrimony_number' => 'PAT-002',
                'current_odometer' => 32000,
                'status'           => 'available',
            ],
            [
                'plate'            => 'GHI-9012',
                'brand'            => 'Volkswagen',
                'model'            => 'Kombi',
                'year'             => 2020,
                'type'             => 'van',
                'patrimony_number' => 'PAT-003',
                'current_odometer' => 48000,
                'status'           => 'in_maintenance',
            ],
        ];

        foreach ($vehicles as $vehicle) {
            Vehicle::create($vehicle);
        }
    }
}