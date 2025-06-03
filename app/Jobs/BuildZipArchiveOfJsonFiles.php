<?php

namespace Modules\Excon\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use Illuminate\Support\Facades\Storage;
use ZipArchive;

use Modules\Excon\Events\AZipArchiveOfJsonFilesWasCreatedEvent;

class BuildZipArchiveOfJsonFiles implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $timestamp = now()->yesterday();
        $foldername = $timestamp->format('Ymd');
        $sourcefolder = Storage::disk("public")->path("");
        logger()->info("Zipping {$sourcefolder} into a single Zip file.");

        $zip = new ZipArchive();
        $filename = "{$foldername}_DR_C2N_Export des donnees de cinematique.zip";
        if ($zip->open(Storage::disk("public")->path("excon/zips/{$filename}"), ZipArchive::CREATE) == TRUE)
        {
            $files = Storage::disk("public")->files("excon/json/{$foldername}");
            foreach($files as $file)
            {
                $fullname = $sourcefolder . $file;
                $relativename = basename($file);
                //logger()->info("{$fullname} -> {$file}");
                $zip->addFile($fullname, $relativename);
            }
            $zip->close();
        }

        event(new AZipArchiveOfJsonFilesWasCreatedEvent(route('skeletor.public_file_download', ['skeletor_download_filename' => "excon/zips/{$filename}"] )));

    }
}
