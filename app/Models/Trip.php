<?php

namespace App\Models;

use App\Enums\TripStatus;
use App\Enums\TripPriority;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Trip extends Model
{
    use HasFactory;

    protected $fillable = [

        'operator_id',
        'driver_id',
        'vehicle_id',

        'patient_name',

        'origin_street',
        'origin_neighborhood',

        'destination_street',
        'destination_neighborhood',
        'destination_city',

        'notes',

        'initial_odometer',
        'final_odometer',

        'started_at',
        'finished_at',

        'status'
    ];

    protected $casts = [

        'status' => TripStatus::class,
        'priority' => TripPriority::class,

        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    public function operator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /*
    |--------------------------------------------------------------------------
    | DOMAIN HELPERS
    |--------------------------------------------------------------------------
    */

    public function isAssigned(): bool
    {
        return $this->status === TripStatus::Assigned;
    }

    public function isInProgress(): bool
    {
        return $this->status === TripStatus::InProgress;
    }

    public function isCompleted(): bool
    {
        return $this->status === TripStatus::Completed;
    }

    /*
    |--------------------------------------------------------------------------
    | CALCULATED VALUES
    |--------------------------------------------------------------------------
    */

    public function getDistanceAttribute(): ?int
    {
        if (!$this->initial_odometer || !$this->final_odometer) {
            return null;
        }

        return $this->final_odometer - $this->initial_odometer;
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->whereIn('status', [
            TripStatus::Assigned,
            TripStatus::InProgress
        ]);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', TripStatus::Completed);
    }

    public function scopeForDriver($query, $driverId)
    {
        return $query->where('driver_id', $driverId);
    }

}