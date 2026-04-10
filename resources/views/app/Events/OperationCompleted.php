<?php

namespace App\Events;

use App\Models\Operation;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OperationCompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Operation $operation;

    /**
     * Create a new event instance.
     */
    public function __construct(Operation $operation)
    {
        $this->operation = $operation;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('operations'),
        ];
    }
}
