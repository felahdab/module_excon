<?php

namespace Modules\Excon\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

use Modules\Excon\Events\AZipArchiveOfJsonFilesWasCreatedEvent;
use Modules\Excon\Emails\ZipFileReadyForDownloadMail;

class SendAZipArchiveToExdirCisListener
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
    public function handle(AZipArchiveOfJsonFilesWasCreatedEvent $event): void
    {
        $destination_email = "clt.exdir-cis@navy-brest.fra.nato.int";
        $source_file = $event->url;

        logger()->info("{$destination_email} should receive: {$source_file}");

        Mail::to($destination_email)
                ->queue(new ZipFileReadyForDownloadMail($source_file));

    }
}
