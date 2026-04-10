<?php

namespace App\Events;

use App\Models\Vehicle;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VehicleAssigned
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Vehicle $vehicle;
    public int $operationId;
    public int $driverId;

    /**
     * Create a new event instance.
     */
    public function __construct(Vehicle $vehicle, int $operationId, int $driverId)
    {
        $this->vehicle = $vehicle;
        $this->operationId = $operationId;
        $this->driverId = $driverId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('fleet'),
        ];
    }
}
