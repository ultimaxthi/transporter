<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Exception;

class UserService
{
    /**
     * Registra um novo usuário no sistema
     */
    public function registerUser(array $data): User
    {
        return DB::transaction(function () use ($data) {
            // Validar papel do usuário
            if (!in_array($data['role'], User::ROLES)) {
                throw new Exception('Papel de usuário inválido.');
            }

            return User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => $data['role'],
                'registration_number' => $data['registration_number'] ?? null,
                'active' => $data['active'] ?? true,
            ]);
        });
    }

    /**
     * Atualiza informações do usuário
     */
    public function updateUser(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {
            // Se estiver atualizando a senha
            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }

            $user->update($data);
            return $user->refresh();
        });
    }

    /**
     * Ativa ou desativa um usuário
     */
    public function toggleUserStatus(User $user, bool $active): User
    {
        return DB::transaction(function () use ($user, $active) {
            // Se for desativar um motorista, verificar se está em viagem
            if (!$active && $user->isDriver() && $user->activeTrip()->exists()) {
                throw new Exception('Não é possível desativar um motorista em viagem.');
            }

            $user->update(['active' => $active]);
            return $user->refresh();
        });
    }

    /**
     * Lista motoristas disponíveis (sem viagem ativa)
     */
    public function getAvailableDrivers()
    {
        return User::where('role', User::ROLE_DRIVER)
            ->where('active', true)
            ->whereDoesntHave('tripsAsDriver', function ($query) {
                $query->whereIn('status', [
                    \App\Enums\TripStatus::Assigned,
                    \App\Enums\TripStatus::InProgress
                ]);
            })
            ->get();
    }

    /**
     * Lista motoristas com veículo atribuído
     */
    public function getDriversWithVehicle()
    {
        return User::where('role', User::ROLE_DRIVER)
            ->where('active', true)
            ->whereHas('activeVehicle')
            ->with('activeVehicle.vehicle')
            ->get();
    }
}