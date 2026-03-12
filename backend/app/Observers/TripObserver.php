<?php

namespace App\Observers;

use App\Models\Trip;
use App\Enums\TripStatus;

class TripObserver
{
    /**
     * Executa antes de criar uma viagem
     */
    public function creating(Trip $trip): void
    {
        // Define o status padrão se não estiver definido
        if (!isset($trip->status)) {
            $trip->status = TripStatus::Scheduled;
        }
    }

    /**
     * Executa após atualizar uma viagem
     */
    public function updated(Trip $trip): void
    {
        // Se a viagem foi finalizada, atualiza estatísticas ou notifica
        if ($trip->isDirty('status') && $trip->status === TripStatus::Completed) {
            // Aqui você poderia disparar eventos, notificações, etc.
        }
    }
}