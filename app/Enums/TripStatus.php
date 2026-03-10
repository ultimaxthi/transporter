<?php

namespace App\Enums;

enum TripStatus: string
{
    case Pending = 'pending';       // criada mas sem motorista
    case Assigned = 'assigned';     // motorista vinculado
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function canStart(): bool
    {
        return $this === self::Assigned;
    }

    public function canFinish(): bool
    {
        return $this === self::InProgress;
    }
}