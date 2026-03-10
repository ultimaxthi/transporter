<?php

namespace App\Services;

use App\Models\Trip;
use App\Models\User;
use App\Enums\TripStatus;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TripDispatchService
{
    public function dispatchPendingTrips(): void
    {
        $pendingTrips = Trip::where('status', TripStatus::Scheduled)
            ->where('created_at', '<=', now()->subMinutes(15))
            ->orderBy('priority', 'desc')
            ->get();

        foreach ($pendingTrips as $trip) {
            $this->assignTrip($trip);
        }
    }

    protected function assignTrip(Trip $trip): void
    {
        DB::transaction(function () use ($trip) {

            $trip = Trip::lockForUpdate()->find($trip->id);

            if ($trip->status !== TripStatus::Scheduled) {
                return;
            }

            $driver = $this->findBestDriver();

            if (!$driver) {
                return;
            }

            $queuePosition = $driver->tripsAsDriver()
                ->whereIn('status', [
                    TripStatus::Assigned,
                    TripStatus::InProgress
                ])
                ->count() + 1;

            $trip->update([
                'driver_id' => $driver->id,
                'status' => TripStatus::Assigned,
                'queue_position' => $queuePosition
            ]);
        });
    }

    protected function findBestDriver(): ?User
    {
        return User::where('role', User::ROLE_DRIVER)
            ->where('active', true)
            ->withCount([
                'tripsAsDriver as active_trips_count' => function ($query) {
                    $query->whereIn('status', [
                        TripStatus::Assigned,
                        TripStatus::InProgress
                    ]);
                }
            ])
            ->orderBy('active_trips_count')
            ->first();
    }
}