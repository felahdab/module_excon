<?php

namespace Modules\Excon\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

use Carbon\Carbon;

class NirisSituationWasRetreivedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Carbon $timestamp;
    /**
     * Create a new event instance.
     */
    public function __construct(public string $datasource, public array $situation)
    {
        $this->timestamp= now();
    }

    /**
     * Get the channels the event should be broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
