<?php

namespace Modules\Excon\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

use Modules\Excon\Models\Engagement;
use Modules\Excon\Models\User;
use App\Models\User as SkeletorUser;

class AnAttackWasReportedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public Engagement $engagement, public User|SkeletorUser $user)
    {
        if (is_a(SkeletorUser::class, $user))
        {
            $this->user = cast_as_eloquent_descendant($user, User::class);
        }
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
