<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use App\Models\Page;

class GenerateImagesForPages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'podcaster:generate-images-for-pages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate and cache responsive images for static pages';

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
        $pageCollection = [
            'home' => [
                'bg',
/*                'blog',
                'hosting',
                'packages',
                'socialmedia',
                'storage',
                'support',
                'statistics',*/
            ],
            'dashboard' => [
                'bg',
            ],
            'faq' => [
                'bg',
            ],
            'spotify' => [
                'bg',
            ],
            'hint' => [
                'teaser',
                'password',
            ],
            'errors' => [
                '401',
                '403',
                '404',
                '410',
                '500',
                '503',
            ],
        ];

        $this->line("Copying originals from " . resource_path('originals') . " to " . storage_path('app/originals'));
        File::copyDirectory(resource_path('originals'), storage_path('app/originals'));
        $this->line("Copied originals.");

        $aPages = Page::all();
        $this->line("Found " . $aPages->count() . " pages.");

        foreach ($aPages as $page) {
            $this->line("Processing page " . $page->title);
            if (array_key_exists($page->title, $pageCollection)) {
                $this->line("Found page '" . $page->title . "' in collections.");
                foreach ($pageCollection[$page->title] as $collection) {
                    $this->line("Collection '{$collection}' of page '" . $page->title . "' found.");
                    $page->clearMediaCollection($collection);
                    $this->line("Collection '{$collection}' of page '" . $page->title . "' cleared.");
                    $path = storage_path("app/originals/{$page->title}/{$collection}/*");
                    $files = File::glob($path);
                    $this->line("Found " . count($files) . " pictures in Collection '{$collection}' of page '" . $page->title . "'.");
                    foreach ($files as $file) {
                        if (File::isFile($file)) {
                            $this->line("Adding {$file} to {$page->title}.");
                            $res = $page
                                ->addMedia($file)
                                ->withResponsiveImages()
                                ->toMediaCollection($collection);
                            if ($res) {
                                $this->line("Added {$file} to {$page->title}.");
                            } else {
                                $this->line("Failed {$file} to {$page->title}.");
                            }
                        }
                    }
                }
            }
        }
    }
}
