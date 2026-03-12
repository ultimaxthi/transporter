<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Trip;
use App\Models\Vehicle;
use App\Models\VehicleMaintenance;
use App\Models\FuelSupply;
use App\Models\User;
use App\Policies\TripPolicy;
use App\Policies\VehiclePolicy;
use App\Policies\VehicleMaintenancePolicy;
use App\Policies\FuelSupplyPolicy;
use App\Policies\UserPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Trip::class => TripPolicy::class,
        Vehicle::class => VehiclePolicy::class,
        VehicleMaintenance::class => VehicleMaintenancePolicy::class,
        FuelSupply::class => FuelSupplyPolicy::class,
        User::class => UserPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Implicitly register all policies
        $this->registerPolicies();
    }
}