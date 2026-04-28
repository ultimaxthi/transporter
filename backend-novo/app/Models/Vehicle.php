<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'plate',
        'brand',
        'model',
        'year',
        'type',
        'patrimony_number',
        'current_odometer',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'year'             => 'integer',
            'current_odometer' => 'integer',
        ];
    }

    // Relationships
    public function drivers()
    {
        return $this->belongsToMany(User::class, 'vehicle_driver')
            ->withPivot(['assigned_at', 'unassigned_at', 'active'])
            ->withTimestamps();
    }

    public function activeDriver()
    {
        return $this->belongsToMany(User::class, 'vehicle_driver')
            ->wherePivot('active', true)
            ->withPivot(['assigned_at', 'unassigned_at', 'active'])
            ->withTimestamps();
    }

    public function trips()
    {
        return $this->hasMany(Trip::class);
    }

    public function maintenances()
    {
        return $this->hasMany(VehicleMaintenance::class);
    }

    public function fuelSupplies()
    {
        return $this->hasMany(FuelSupply::class);
    }
}