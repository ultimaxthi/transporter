<?php

namespace App\Policies;

use App\Models\Vehicle;
use App\Models\User;

class VehiclePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Vehicle $vehicle): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Vehicle $vehicle): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Vehicle $vehicle): bool
    {
        return $user->isAdmin();
    }

    public function restore(User $user, Vehicle $vehicle): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, Vehicle $vehicle): bool
    {
        return $user->isAdmin();
    }

    public function assignDriver(User $user, Vehicle $vehicle): bool
    {
        return $user->isAdmin();
    }
}