<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FuelSupply extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'driver_id',
        'fuel_station',
        'fuel_type',
        'liters',
        'price_per_liter',
        'total_cost',
        'odometer',
        'supplied_at',
    ];

    protected function casts(): array
    {
        return [
            'liters'          => 'decimal:2',
            'price_per_liter' => 'decimal:2',
            'total_cost'      => 'decimal:2',
            'supplied_at'     => 'datetime',
        ];
    }

    // Relationships
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }
}