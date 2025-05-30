<?php

namespace Modules\Excon\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use Carbon\Carbon;

use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action as NotificationAction;

use App\Models\User;

use Modules\Excon\Models\Unit;
use Modules\Excon\Services\AntaresExportService;


class AntaresExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $filename;
    protected string $filepath;
    public string $url;
    
    /**
     * Create a new job instance.
     */
    public function __construct(public User $user, public Unit $unit, public Carbon $start_timestamp, public Carbon $stop_timestamp)
    {
        $snake_unit_name = Str::snake($unit->name);
        $this->filename = "{$snake_unit_name}_{$start_timestamp->format('YmdHs')}_{$stop_timestamp->format('YmdHs')}.txt"; 

        $this->filepath = Storage::disk('public')->path("excon/{$this->filename}");
        $this->url = Storage::disk('public')->url("excon/{$this->filename}");
    }


    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $lines = AntaresExportService::make()
                        ->exportAsAntares($this->unit, $this->start_timestamp, $this->stop_timestamp);

        $content = "";
        foreach($lines as $line)
        {
            $content = $content . $line . "\n";
        }

        Storage::disk('public')->put("excon/{$this->filename}", $content);

        Notification::make()
            ->title("Export completed")
            ->body("The export for {$this->unit->name} between {$this->start_timestamp} and {$this->stop_timestamp} is completed.")
            ->actions([
                NotificationAction::make('download')
                    ->label("Download your file")
                    ->url($this->url,  shouldOpenInNewTab: true)
                    ->markAsRead()
            ])
            ->sendToDatabase($this->user, isEventDispatched: true);
    }
}

