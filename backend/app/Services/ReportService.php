<?php

namespace App\Services;

use App\Models\Vehicle;
use App\Models\Trip;
use App\Models\User;
use App\Models\FuelSupply;
use App\Enums\TripStatus;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * Gera relatório de consumo de combustível por veículo
     */
    public function getFuelConsumptionReport(Carbon $startDate, Carbon $endDate): Collection
    {
        return Vehicle::with(['fuelSupplies' => function ($query) use ($startDate, $endDate) {
            $query->whereBetween('supplied_at', [$startDate, $endDate]);
        }])
        ->get()
        ->map(function ($vehicle) {
            $totalLiters = $vehicle->fuelSupplies->sum('liters');
            $totalCost = $vehicle->fuelSupplies->sum('total_cost');

            return [
                'vehicle_id' => $vehicle->id,
                'plate' => $vehicle->plate,
                'model' => $vehicle->model,
                'total_liters' => $totalLiters,
                'total_cost' => $totalCost,
                'average_consumption' => $vehicle->averageConsumption(),
                'supplies_count' => $vehicle->fuelSupplies->count(),
            ];
        });
    }

    /**
     * Gera relatório de desempenho de motoristas
     */
    public function getDriverPerformanceReport(Carbon $startDate, Carbon $endDate): Collection
    {
        return User::where('role', User::ROLE_DRIVER)
            ->with(['tripsAsDriver' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('finished_at', [$startDate, $endDate])
                    ->where('status', TripStatus::Completed);
            }])
            ->get()
            ->map(function ($driver) {
                $completedTrips = $driver->tripsAsDriver;
                $totalDistance = $completedTrips->sum('distance');
                $totalTrips = $completedTrips->count();

                return [
                    'driver_id' => $driver->id,
                    'driver_name' => $driver->name,
                    'total_trips' => $totalTrips,
                    'total_distance' => $totalDistance,
                    'average_distance' => $totalTrips > 0 ? round($totalDistance / $totalTrips, 2) : 0,
                    'fuel_efficiency' => $this->calculateDriverFuelEfficiency($driver->id, $startDate, $endDate),
                ];
            });
    }

    /**
     * Calcula a eficiência de combustível de um motorista
     */
    protected function calculateDriverFuelEfficiency(int $driverId, Carbon $startDate, Carbon $endDate): ?float
    {
        $supplies = FuelSupply::where('driver_id', $driverId)
            ->whereBetween('supplied_at', [$startDate, $endDate])
            ->get();

        $totalLiters = $supplies->sum('liters');

        $trips = Trip::where('driver_id', $driverId)
            ->whereBetween('finished_at', [$startDate, $endDate])
            ->where('status', TripStatus::Completed)
            ->get();

        $totalDistance = $trips->sum('distance');

        if ($totalLiters > 0 && $totalDistance > 0) {
            return round($totalDistance / $totalLiters, 2);
        }

        return null;
    }

    /**
     * Gera relatório de viagens por período
     */
    public function getTripReport(Carbon $startDate, Carbon $endDate): array
    {
        $trips = Trip::whereBetween('created_at', [$startDate, $endDate])
            ->with(['driver', 'operator', 'vehicle'])
            ->get();

        $completedTrips = $trips->where('status', TripStatus::Completed);
        $canceledTrips = $trips->where('status', TripStatus::Canceled);

        $totalDistance = $completedTrips->sum('distance');
        $averageDistance = $completedTrips->count() > 0 
            ? round($totalDistance / $completedTrips->count(), 2) 
            : 0;

        return [
            'total_trips' => $trips->count(),
            'completed_trips' => $completedTrips->count(),
            'canceled_trips' => $canceledTrips->count(),
            'pending_trips' => $trips->count() - $completedTrips->count() - $canceledTrips->count(),
            'total_distance' => $totalDistance,
            'average_distance' => $averageDistance,
            'trips_by_day' => $this->getTripsGroupedByDay($trips),
        ];
    }

    /**
     * Agrupa viagens por dia
     */
    protected function getTripsGroupedByDay(Collection $trips): array
    {
        return $trips->groupBy(function ($trip) {
            return $trip->created_at->format('Y-m-d');
        })
        ->map(function ($dayTrips) {
            return [
                'count' => $dayTrips->count(),
                'completed' => $dayTrips->where('status', TripStatus::Completed)->count(),
                'distance' => $dayTrips->where('status', TripStatus::Completed)->sum('distance'),
            ];
        })
        ->toArray();
    }
}