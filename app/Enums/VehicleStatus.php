<?php

namespace App\Enums;

enum VehicleStatus: string
{
    case AVAILABLE = 'available';
    case IN_TRIP = 'in_trip';
    case IN_MAINTENANCE = 'in_maintenance';
    case INACTIVE = 'inactive';

    /*
    |--------------------------------------------------------------------------
    | Labels para exibição
    |--------------------------------------------------------------------------
    */

    public function label(): string
    {
        return match ($this) {
            self::AVAILABLE => 'Disponível',
            self::IN_TRIP => 'Em Viagem',
            self::IN_MAINTENANCE => 'Em Manutenção',
            self::INACTIVE => 'Inativo',
        };
    }

    /*
    |--------------------------------------------------------------------------
    | Lista para formulários
    |--------------------------------------------------------------------------
    */

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn ($case) => [
                $case->value => $case->label()
            ])
            ->toArray();
    }

    /*
    |--------------------------------------------------------------------------
    | Apenas valores
    |--------------------------------------------------------------------------
    */

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}