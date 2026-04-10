<?php

namespace App\Events;

use App\Models\JuridiqueContrat;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class JuridiqueContratCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $contrat;

    public function __construct(JuridiqueContrat $contrat)
    {
        $this->contrat = $contrat;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('juridique');
    }
}
