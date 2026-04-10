<?php

namespace App\Events;

use App\Models\FinancementDossier;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FinancingDossierCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $dossier;

    public function __construct(FinancementDossier $dossier)
    {
        $this->dossier = $dossier;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('juridique');
    }
}
