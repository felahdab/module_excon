<?php

namespace Modules\Excon\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

use Modules\Excon\Events\AnAttackWasReportedEvent;
use Modules\Excon\Emails\AnAttackWasReportedMail;

use App\Models\User;

class AnAttackWasReportedListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(AnAttackWasReportedEvent $event): void
    {
        $users_to_notify = User::permission("excon::notify_when_attack_reported")->get();

        foreach($users_to_notify as $recipient)
        {
            Mail::to($recipient)
                ->queue(new AnAttackWasReportedMail($event->engagement, $event->user));
        }
    }
}
