<?php

namespace App\Enums;

enum TripPriority: string
{
    case Normal = 'normal';
    case High = 'high';
    case Emergency = 'emergency';
}