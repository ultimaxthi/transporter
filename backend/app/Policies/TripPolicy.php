<?php

namespace App\Policies;

use App\Models\Trip;
use App\Models\User;

class TripPolicy
{
    /**
     * Ver lista de viagens
     */
    public function viewAny(User $user): bool
    {
        return true; 
        // ou restringir por role se quiser
    }

    /**
     * Ver viagem específica
     */
    public function view(User $user, Trip $trip): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        return $trip->driver_id === $user->id;
    }

    /**
     * Criar viagem
     */
    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Atualizar viagem
     */
    public function update(User $user, Trip $trip): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Deletar viagem
     */
    public function delete(User $user, Trip $trip): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Restaurar viagem
     */
    public function restore(User $user, Trip $trip): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Forçar delete
     */
    public function forceDelete(User $user, Trip $trip): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Iniciar viagem
     */
    public function start(User $user, Trip $trip): bool
    {
        return $user->role === 'admin' || $trip->driver_id === $user->id;
    }

    /**
     * Finalizar viagem
     */
    public function finish(User $user, Trip $trip): bool
    {
        return $user->role === 'admin' || $trip->driver_id === $user->id;
    }
}