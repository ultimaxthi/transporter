<?php

namespace App\Enums;

enum MaintenanceType: string
{
    case PREVENTIVE = 'preventive';
    case CORRECTIVE = 'corrective';
    case INSPECTION = 'inspection';
    case OIL_CHANGE = 'oil_change';
    case MECHANICAL = 'mechanical';
    case ELECTRICAL = 'electrical';

    /*
    |--------------------------------------------------------------------------
    | Label para exibição
    |--------------------------------------------------------------------------
    */

    public function label(): string
    {
        return match ($this) {
            self::PREVENTIVE => 'Manutenção Preventiva',
            self::CORRECTIVE => 'Manutenção Corretiva',
            self::INSPECTION => 'Inspeção',
            self::OIL_CHANGE => 'Troca de Óleo',
            self::MECHANICAL => 'Manutenção Mecânica',
            self::ELECTRICAL => 'Manutenção Elétrica',
        };
    }

    /*
    |--------------------------------------------------------------------------
    | Lista para selects / formulários
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
}