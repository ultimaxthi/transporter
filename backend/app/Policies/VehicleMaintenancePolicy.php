<?php

namespace App\Policies;

use App\Models\VehicleMaintenance;
use App\Models\User;

class VehicleMaintenancePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isOperator();
    }

    public function view(User $user, VehicleMaintenance $maintenance): bool
    {
        return $user->isAdmin() || $user->isOperator();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isOperator();
    }

    public function update(User $user, VehicleMaintenance $maintenance): bool
    {
        return $user->isAdmin() || ($user->id === $maintenance->created_by);
    }

    public function delete(User $user, VehicleMaintenance $maintenance): bool
    {
        return $user->isAdmin();
    }

    public function finish(User $user, VehicleMaintenance $maintenance): bool
    {
        return $user->isAdmin() || ($user->id === $maintenance->created_by);
    }
}