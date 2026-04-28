<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_name',
        'priority',
        'origin_street',
        'origin_neighborhood',
        'destination_street',
        'destination_neighborhood',
        'destination_city',
        'observations',
        'status',
        'queue_position',
        'operator_id',
        'driver_id',
        'vehicle_id',
        'initial_odometer',
        'final_odometer',
        'started_at',
        'finished_at',
    ];

    protected function casts(): array
    {
        return [
            'started_at'  => 'datetime',
            'finished_at' => 'datetime',
        ];
    }

    // Relationships
    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}