<?php

namespace App\Policies;

use App\Models\FuelSupply;
use App\Models\User;

class FuelSupplyPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isOperator();
    }

    public function view(User $user, FuelSupply $supply): bool
    {
        return $user->isAdmin() || $user->isOperator() || $user->id === $supply->driver_id;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isOperator() || $user->isDriver();
    }

    public function update(User $user, FuelSupply $supply): bool
    {
        return $user->isAdmin() || ($user->isOperator() && $supply->created_at->diffInHours() < 24);
    }

    public function delete(User $user, FuelSupply $supply): bool
    {
        return $user->isAdmin();
    }
}