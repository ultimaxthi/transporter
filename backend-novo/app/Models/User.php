<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, TwoFactorAuthenticatable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'registration_number',
        'active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at'     => 'datetime',
            'password'              => 'hashed',
            'active'                => 'boolean',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    // Relationships
    public function vehicles()
    {
        return $this->belongsToMany(Vehicle::class, 'vehicle_driver')
            ->withPivot(['assigned_at', 'unassigned_at', 'active'])
            ->withTimestamps();
    }

    public function trips()
    {
        return $this->hasMany(Trip::class, 'driver_id');
    }

    public function operatedTrips()
    {
        return $this->hasMany(Trip::class, 'operator_id');
    }
}