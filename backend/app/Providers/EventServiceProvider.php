<?php

namespace App\Providers;

use App\Models\FuelSupply;
use App\Models\Trip;
use App\Models\VehicleDriver;
use App\Models\VehicleMaintenance;
use App\Observers\FuelSupplyObserver;
use App\Observers\TripObserver;
use App\Observers\VehicleDriverObserver;
use App\Observers\VehicleMaintenanceObserver;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        FuelSupply::observe(FuelSupplyObserver::class);
        Trip::observe(TripObserver::class);
        VehicleMaintenance::observe(VehicleMaintenanceObserver::class);
        VehicleDriver::observe(VehicleDriverObserver::class);
    }
}