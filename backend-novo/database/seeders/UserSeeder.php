<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::create([
            'name'                => 'Administrador',
            'email'               => 'admin@viage.com',
            'password'            => Hash::make('password'),
            'role'                => 'admin',
            'registration_number' => 'ADM-001',
            'active'              => true,
        ]);

        // Operador
        User::create([
            'name'                => 'Operador Teste',
            'email'               => 'operador@viage.com',
            'password'            => Hash::make('password'),
            'role'                => 'operator',
            'registration_number' => 'OPR-001',
            'active'              => true,
        ]);

        // Motorista
        User::create([
            'name'                => 'Motorista Teste',
            'email'               => 'motorista@viage.com',
            'password'            => Hash::make('password'),
            'role'                => 'driver',
            'registration_number' => 'MOT-001',
            'active'              => true,
        ]);
    }
}