<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use App\Models\Feed;
use App\Models\Show;

class LinkUnpublishedShows extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'podcaster:link-unpublished-shows';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a symlink for all shows saved as draft';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
/*        Feed::select(['username', 'feed_id', 'entries'])->chunk(50, function ($feeds) {
            foreach ($feeds as $feed) {*/
        foreach (Feed::select(['username', 'feed_id', 'entries'])->cursor() as $feed) {
            if ($feed->entries) {
                $unpublished = array_filter($feed->entries, function ($v) {
                    return isset($v['is_public']) && $v['is_public'] != Show::PUBLISH_NOW
                        && isset($v['show_media']) && $v['show_media'];
                });
                if (count($unpublished) > 0) {
                    try {
                        $username = $feed->username;
                        $basePath = storage_path(get_user_public_path($username));
                        $feedPath = $basePath.DIRECTORY_SEPARATOR.$feed->feed_id.DIRECTORY_SEPARATOR.'media';
                        $logoPath = $basePath.DIRECTORY_SEPARATOR.$feed->feed_id.DIRECTORY_SEPARATOR.'logos';

                        File::ensureDirectoryExists($feedPath);
                        File::ensureDirectoryExists($logoPath);

                        foreach ($unpublished as $show) {
                            $this->link($username, $show['show_media'], $feedPath);

                            if (isset($show['itunes']['logo']) && $show['itunes']['logo']) {
                                $this->link($username, $show['show_media'], $logoPath);
                            }
                        }
                    } catch (\Exception $e) {
                        Log::error($e->getMessage());
                        Log::error($e->getTraceAsString());
                    }
                }
            }
        }
/*            }
        });*/
    }

    private function link(string $username, string $id, string $path)
    {
        try {
            $file = get_file($username, $id);
            $link = $path.DIRECTORY_SEPARATOR.$file['name'];
            if (!File::isFile($link)) {
                File::link($file['path'], $link);
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
        }
    }
}
