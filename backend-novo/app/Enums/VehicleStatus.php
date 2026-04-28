<?php

namespace App\Enums;

enum VehicleStatus: string
{
    case Available      = 'available';
    case InTrip         = 'in_trip';
    case InMaintenance  = 'in_maintenance';
    case Inactive       = 'inactive';
}