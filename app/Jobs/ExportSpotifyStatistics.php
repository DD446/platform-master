<?php

namespace App\Jobs;

use Cake\Chronos\Date;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Events\SpotifyStatisticsExportStarted;
use App\Events\SpotifyStatisticsExportCreated;
use App\Models\SpotifyAnalytic;
use App\Models\SpotifyAnalyticsExport;
use App\Scopes\UserScope;
use ZipArchive;

class ExportSpotifyStatistics implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var SpotifyAnalyticsExport
     */
    public $spotifyAnalyticsExport;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(SpotifyAnalyticsExport $spotifyAnalyticsExport)
    {
        //
        $this->spotifyAnalyticsExport = $spotifyAnalyticsExport;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        event(new SpotifyStatisticsExportStarted($this->spotifyAnalyticsExport));

        $path = SpotifyAnalyticsExport::getPath($this->spotifyAnalyticsExport);

        if (!File::isDirectory($path)) {
            File::makeDirectory($path, 0755, true);
        }

        // Fetch data from DB
        $filename = $this->spotifyAnalyticsExport->id . SpotifyAnalyticsExport::DEFAULT_EXTENSION;
        $showTitle = $this->spotifyAnalyticsExport->show_title;
        $start = $this->spotifyAnalyticsExport->start;
        $end = $this->spotifyAnalyticsExport->end;

        // Create ZipArchive Obj
        $zip = new ZipArchive();

        if (true === $zip->open($path .  $filename, ZipArchive::CREATE)) {
            $zip->addFromString('README.txt', trans('spotify.statistics_readme'));
            // Add File in ZipArchive
            SpotifyAnalytic::withoutGlobalScope(UserScope::class)
                ->where('user_id', '=', $this->spotifyAnalyticsExport->user_id)
                ->whereBetween('date', [$start, $end])
                ->when($showTitle, function ($query, $showTitle) {
                    return $query->where('show_title', '=', $showTitle);
                })
                ->chunk(100, function($data) use ($zip) {
                foreach ($data as $row) {
                    $zip->addFromString(Str::slug($row->show_title) . '_' . \date('Y-m-d', strtotime($row->date)) . '.json', $row->data);
                }
            });

            // Close ZipArchive
            $zip->close();
        }

        if (File::isFile($path .  $filename)) {
            $this->spotifyAnalyticsExport->update(['is_exported' => true]);
        } else {
            $this->spotifyAnalyticsExport->delete();
        }
        event(new SpotifyStatisticsExportCreated($this->spotifyAnalyticsExport));
    }
}
