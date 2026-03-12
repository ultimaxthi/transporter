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
        return true; // Todos os usuários autenticados podem ver a lista
    }

    /**
     * Ver viagem específica
     */
    public function view(User $user, Trip $trip): bool
    {
        if ($user->isAdmin() || $user->isOperator()) {
            return true;
        }

        return $user->isDriver() && $trip->driver_id === $user->id;
    }

    /**
     * Criar viagem
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isOperator();
    }

    /**
     * Atualizar viagem
     */
    public function update(User $user, Trip $trip): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isOperator() && $trip->operator_id === $user->id;
    }

    /**
     * Deletar viagem
     */
    public function delete(User $user, Trip $trip): bool
    {
        return $user->isAdmin();
    }

    /**
     * Restaurar viagem
     */
    public function restore(User $user, Trip $trip): bool
    {
        return $user->isAdmin();
    }

    /**
     * Forçar delete
     */
    public function forceDelete(User $user, Trip $trip): bool
    {
        return $user->isAdmin();
    }

    /**
     * Iniciar viagem
     */
    public function start(User $user, Trip $trip): bool
    {
        return $user->isAdmin() || $trip->driver_id === $user->id;
    }

    /**
     * Finalizar viagem
     */
    public function finish(User $user, Trip $trip): bool
    {
        return $user->isAdmin() || $trip->driver_id === $user->id;
    }

    /**
     * Cancelar viagem
     */
    public function cancel(User $user, Trip $trip): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isOperator() && $trip->operator_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Atribuir motorista à viagem
     */
    public function assignDriver(User $user, Trip $trip): bool
    {
        return $user->isAdmin() || $user->isOperator();
    }
}