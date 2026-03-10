<?php

namespace App\Models;

use App\Enums\TripStatus;
use App\Enums\VehicleStatus;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Vehicle extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */
    protected $fillable = [
        'plate',
        'brand',
        'model',
        'year',
        'type',
        'patrimony_number',
        'current_odometer',
    ];
    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'current_odometer' => 'integer',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

        public function scopeAvailable(Builder $query): Builder
        {
            return $query
                ->whereDoesntHave('activeMaintenance')
                ->whereDoesntHave('activeTrip');
        }

    /*
    |--------------------------------------------------------------------------
    | Relacionamentos
    |--------------------------------------------------------------------------
    */

    //Manutenções
    public function maintenances()
    {
        return $this->hasMany(VehicleMaintenance::class);
    }

    public function activeMaintenance()
    {
        return $this->hasOne(VehicleMaintenance::class)
            ->where('start_date', '<=', now())
            ->where(function ($query) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', now());
            });
    }


    //Viagens
    public function trips()
    {
        return $this->hasMany(Trip::class);
    }

    public function activeTrip()
    {
        return $this->hasOne(Trip::class)
            ->whereNull('finished_at')
            ->whereIn('status', [
                TripStatus::Assigned,
                TripStatus::InProgress
            ]);
    }
    
    //Vinculo com o motorista
    public function drivers()
    {
        return $this->belongsToMany(
            User::class,
            'vehicle_driver',
            'vehicle_id',
            'driver_id',
        )->withPivot(['assigned_at', 'unassigned_at', 'active'])
         ->withTimestamps();
    }

    public function activeDriver()
    {
        return $this->hasOne(VehicleDriver::class)->where('active', true);
    }

    public function fuelSupplies()
    {
        return $this->hasMany(FuelSupply::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers Inteligentes
    |--------------------------------------------------------------------------
    */

    public function isUnderMaintenance(): bool
    {
        return $this->activeMaintenance()->exists();
    }

    public function isInTrip(): bool
    {
        return $this->activeTrip()->exists();
    }

    public function isAssigned(): bool
    {
        return $this->activeDriver()->exists();
    }

    public function isAvailable(): bool
    {
       return !$this->isUnderMaintenance()
            && !$this->isInTrip()
            && $this->isAssigned();
    }

    protected function formattedPlate(): Attribute
    {
        return Attribute::make(
            get: fn () => strtoupper($this->plate),
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Relatórios e Métricas
    |--------------------------------------------------------------------------
    */

    public function totalKilometersByMonth(Carbon $date): int
    {
        return (int) $this->trips()
            ->whereMonth('finished_at', $date->month)
            ->whereYear('finished_at', $date->year)
            ->whereNotNull('final_odometer')
            ->where('status', TripStatus::Completed)
            ->sum(\DB::raw('final_odometer - initial_odometer'));
    }

    public function averageConsumption(): ?float
    {
        $totalKm = $this->trips()
            ->whereNotNull('final_odometer')
            ->where('status', TripStatus::Completed)
            ->sum(\DB::raw('final_odometer - initial_odometer'));

        $totalLiters = $this->fuelSupplies()->sum('liters');

        if($totalLiters == 0) {
            return null;
        }

        return round($totalKm / $totalLiters, 2);
    }
}
