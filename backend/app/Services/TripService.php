<?php

namespace App\Services;
use App\Enums\TripStatus;
use App\Models\Trip;
use Illuminate\Support\Facades\DB;
use Exception;

class TripService
{

    public function startTrip(Trip $trip, int $initialOdometer): Trip
    {
        return DB::transaction(function () use ($trip, $initialOdometer) {

            $trip->refresh();

            if (!$trip->status->canStart()) {
                throw new Exception('A viagem não está disponível para iniciar.');
            }

            $vehicle = $trip->vehicle()->lockForUpdate()->first();

            if ($vehicle->isUnderMaintenance()) {
                throw new Exception('Veículo está em manutenção.');
            }

            if ($initialOdometer < $vehicle->current_odometer) {
                throw new Exception('O odômetro inicial é menor que o atual.');
            }

            $driver = $trip->driver()->lockForUpdate()->firstOrFail();

            $driverHasActiveTrip = $driver
                ->tripsAsDriver()
                ->where('status', TripStatus::InProgress)
                ->exists();

            if ($driverHasActiveTrip) {
                throw new Exception('Motorista já possui viagem em andamento.');
            }

            $trip->update([
                'initial_odometer' => $initialOdometer,
                'started_at'       => now(),
                'status'           => TripStatus::InProgress,
            ]);

            return $trip->refresh();
        });
    }

    public function finishTrip(Trip $trip, int $finalOdometer): Trip
    {
        return DB::transaction(function () use ($trip, $finalOdometer) {

            $trip = Trip::lockForUpdate()->findOrFail($trip->id);

            if (!$trip->status->canFinish()) {
                throw new Exception('A viagem não está em andamento.');
            }

            if ($finalOdometer <= $trip->initial_odometer) {
                throw new Exception('O odômetro final deve ser maior que o inicial.');
            }

            $vehicle = $trip->vehicle()->lockForUpdate()->first();

            $trip->update([
                'final_odometer' => $finalOdometer,
                'finished_at'    => now(),
                'status'         => TripStatus::Completed,
            ]);

            $vehicle->update([
                'current_odometer' => $finalOdometer,
            ]);

            return $trip->refresh();
        });
    }
}
