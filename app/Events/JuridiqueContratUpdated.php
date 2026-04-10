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

class JuridiqueContratUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $contrat;
    public $changes;

    public function __construct(JuridiqueContrat $contrat, array $changes)
    {
        $this->contrat = $contrat;
        $this->changes = $changes;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('juridique');
    }
}
