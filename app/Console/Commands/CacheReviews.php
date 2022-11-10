<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManager;
use App\Models\Feed;
use App\Models\Review;
use App\Models\UserData;
use App\Scopes\OwnerScope;

class CacheReviews extends Command
{
    const PATH_LOGOS = "app/public/reviews/images";
    const PATH_TESTAMONIALS = "app/public/reviews/testamonials";

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'podcaster:cache-reviews';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Erstellt Liste von Meinungen aktiver Benutzer und speichert die Liste zwischen.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $path = storage_path(self::PATH_LOGOS);
        File::ensureDirectoryExists($path);
        $path = storage_path(self::PATH_TESTAMONIALS);
        File::ensureDirectoryExists($path);
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        //$reviews = Review::published()->get();
        $reviews = Review::with('user')->published()->get();

        $aReviews = [];

        foreach($reviews as $review) {

            // Skip entry if user is not available
            if (!$review->user || $review->user->is_acct_active != 1) {
                continue;
            }

            $username = $review->user->username;
            $feed = Feed::where('username', '=', $username)
                ->select(['rss.title', 'logo.itunes'])
                ->first();

            if (!isset($feed->logo)) {
                continue;
            }

            $mediaId = $feed->logo['itunes'];
            $title = $feed->rss['title'];

            // Skip if feed is redirected (user probably switched service)
            // TODO: Implement

            // Skip entry if no logo is set
            if (!$mediaId) {
                continue;
            }

            $name = trim($review->user->first_name);

            // Skip entry if podcaster provided no first name
            if (!$name) {
                continue;
            }

            $logo = UserData::get($mediaId, $username, true);

            // Skip entry if there is no logo available
            if (is_null($logo)) {
                continue;
            }

            $manager = new ImageManager();
            // to finally create image instances
            $image = $manager->make($logo)->resize(150,150);
            $image->save(storage_path(self::PATH_LOGOS . "/{$username}.png"));
            // to finally create image instances
            $image = $manager->make($logo)->resize(350,350);
            $image->save(storage_path(self::PATH_TESTAMONIALS . "/{$username}.png"));

            $aReviews[] = [
                'cite' => $review->published_cite,
                'name' => $name,
                'username' => $username,
                'podcast' => $title,
                'logo' => "{$username}.png",
            ];
        }

        Cache::forever(Review::REVIEW_CACHE_KEY_LIST_WITH_LOGO, $aReviews);
    }
}
