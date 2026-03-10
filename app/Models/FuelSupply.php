<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FuelSupply extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'vehicle_id',
        'driver_id',
        'liters',
        'price_per_liter',
        'total_cost',
        'odometer',
        'supplied_at',
        'fuel_station',
        'fuel_type',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [
            'liters' => 'float',
            'price_per_liter' => 'float',
            'total_cost' => 'float',
            'odometer' => 'integer',
            'supplied_at' => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relacionamentos
    |--------------------------------------------------------------------------
    */

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeForVehicle($query, int $vehicleId)
    {
        return $query->where('vehicle_id', $vehicleId);
    }

    public function scopeRecent($query)
    {
        return $query->orderByDesc('supplied_at');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function calculateTotal(): float
    {
        return round($this->liters * $this->price_per_liter, 2);
    }

    /*
    |--------------------------------------------------------------------------
    | Métricas
    |--------------------------------------------------------------------------
    */

    public function previousSupply(): ?self
    {
        return self::where('vehicle_id', $this->vehicle_id)
            ->where('supplied_at', '<', $this->supplied_at)
            ->orderByDesc('supplied_at')
            ->first();
    }

    public function kilometersSinceLastSupply(): ?int
    {
        $previous = $this->previousSupply();

        if (!$previous) {
            return null;
        }

        return $this->odometer - $previous->odometer;
    }

    public function consumptionKmPerLiter(): ?float
    {
        $km = $this->kilometersSinceLastSupply();

        if (!$km || $this->liters == 0) {
            return null;
        }

        return round($km / $this->liters, 2);
    }

    public function costPerKm(): ?float
    {
        $km = $this->kilometersSinceLastSupply();

        if (!$km || $this->total_cost == 0) {
            return null;
        }

        return round($this->total_cost / $km, 2);
    }
}