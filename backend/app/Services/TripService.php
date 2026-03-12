<?php

namespace App\Services;
use App\Enums\TripStatus;
use App\Models\Trip;
use Illuminate\Support\Facades\DB;
use Exception;

class TripService
{
        /**
     * Cria uma nova viagem no sistema
     */
    public function createTrip(array $data): Trip
    {
        return DB::transaction(function () use ($data) {
            // Validar operador
            $operator = User::where('id', $data['operator_id'])
                ->where('role', User::ROLE_OPERATOR)
                ->firstOrFail();

            // Se tiver driver_id, validar motorista
            if (isset($data['driver_id'])) {
                $driver = User::where('id', $data['driver_id'])
                    ->where('role', User::ROLE_DRIVER)
                    ->where('active', true)
                    ->firstOrFail();
            }

            // Se tiver vehicle_id, validar veículo
            if (isset($data['vehicle_id'])) {
                $vehicle = Vehicle::findOrFail($data['vehicle_id']);

                if ($vehicle->isUnderMaintenance()) {
                    throw new Exception('Veículo está em manutenção.');
                }
            }

            // Definir status inicial
            $status = isset($data['driver_id']) 
                ? TripStatus::Assigned 
                : TripStatus::Scheduled;

            // Criar a viagem
            $trip = Trip::create([
                'operator_id' => $data['operator_id'],
                'driver_id' => $data['driver_id'] ?? null,
                'vehicle_id' => $data['vehicle_id'] ?? null,
                'patient_name' => $data['patient_name'],
                'origin_street' => $data['origin_street'],
                'origin_neighborhood' => $data['origin_neighborhood'],
                'destination_street' => $data['destination_street'],
                'destination_neighborhood' => $data['destination_neighborhood'],
                'destination_city' => $data['destination_city'] ?? null,
                'notes' => $data['notes'] ?? null,
                'priority' => $data['priority'] ?? TripPriority::Normal,
                'status' => $status,
            ]);

            return $trip->refresh();
        });
    }

    /**
     * Cancela uma viagem
     */
    public function cancelTrip(Trip $trip, string $reason = null): Trip
    {
        return DB::transaction(function () use ($trip, $reason) {
            $trip = Trip::lockForUpdate()->findOrFail($trip->id);

            if ($trip->isCompleted()) {
                throw new Exception('Não é possível cancelar uma viagem já finalizada.');
            }

            if ($trip->isInProgress()) {
                throw new Exception('Não é possível cancelar uma viagem em andamento.');
            }

            $trip->update([
                'status' => TripStatus::Canceled,
                'cancellation_reason' => $reason,
                'canceled_at' => now(),
            ]);

            return $trip->refresh();
        });
    }
        /**
     * Inicia uma viagem
     */
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
        /**
     * Termina uma viagem
     */
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
