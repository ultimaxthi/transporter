<?php

namespace App\Models;

use App\Enums\TripStatus;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /*
    |--------------------------------------------------------------------------
    | Roles do Sistema
    |--------------------------------------------------------------------------
    */

    public const ROLE_ADMIN    = 'admin';
    public const ROLE_OPERATOR = 'operator';
    public const ROLE_DRIVER   = 'driver';

    public const ROLES = [
        self::ROLE_ADMIN,
        self::ROLE_OPERATOR,
        self::ROLE_DRIVER,
    ];

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'registration_number',
        'active',
    ];

    /*
    |--------------------------------------------------------------------------
    | Hidden Fields
    |--------------------------------------------------------------------------
    */
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at'         => 'datetime',
            'password'                  => 'hashed',
            'active'                    => 'boolean',
            'two_factor_confirmed_at'   => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes (Consultas Profissionais)
    |--------------------------------------------------------------------------
    */

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }

    public function scopeDrivers(Builder $query): Builder
    {
        return $query->where('role', self::ROLE_DRIVER);
    }

    public function scopeOperators(Builder $query): Builder
    {
        return $query->where('role', self::ROLE_OPERATOR);
    }

    public function scopeAdmins(Builder $query): Builder
    {
        return $query->where('role', self::ROLE_ADMIN);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers de Role
    |--------------------------------------------------------------------------
    */

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isOperator(): bool
    {
        return $this->role === self::ROLE_OPERATOR;
    }

    public function isDriver(): bool
    {
        return $this->role === self::ROLE_DRIVER;
    }

    /*
    |--------------------------------------------------------------------------
    | Relacionamentos - Sistema de Transporte
    |--------------------------------------------------------------------------
    */

    //Operador criou viagens
    public function tripsCreated()
    {
        return $this->hasMany(Trip::class, 'created_by');
    }

    //Motorista é vinculado a viagens
    public function tripsAsDriver()
    {
        return $this->hasMany(Trip::class, 'driver_id');
    }

    // Viagem ativa 
    public function activeTrip()
    {
        return $this->hasOne(Trip::class, 'driver_id')->whereIn('status', [TripStatus::Assigned, TripStatus::InProgress]);
    }

    // Veiculo atualmente vinculado ao motorista
    public function activeVehicle()
    {
        return $this->hasOne(VehicleDriver::class, 'driver_id')->where('active', true)->whereNull('unassigned_at');
    }

    // Abastecimentos realizados
    public function fuelSupplies()
    {
        return $this->hasMany(FuelSupply::class, 'driver_id');
    }

        public function vehicles()
    {
        return $this->belongsToMany(
            Vehicle::class,
            'vehicle_driver',
            'driver_id',
            'vehicle_id'
        )->withPivot(['assigned_at', 'unassigned_at', 'active'])
        ->withTimestamps();
    }
}
