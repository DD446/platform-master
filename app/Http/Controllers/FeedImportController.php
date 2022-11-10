<?php

namespace App\Http\Controllers;

use App\Classes\Datacenter;
use App\Classes\MediaManager;
use App\Rules\IsPodcastFeed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Laminas\Feed\Reader\Reader;
use App\Classes\FeedReader;

class FeedImportController extends Controller
{
    public function check(Request $request)
    {
        $validated = $this->validate($request, [
            'url' => ['required', 'url', 'active_url', new IsPodcastFeed]
        ],[],[
            'url' => trans('feeds.validation_attribute_link'),
        ]);

        $url = $validated['url'];
        $res = [
            'passed' => false,
            'feed' => null,
            'message' => trans('feeds.message_error_import_feed_check')
        ];

        try {
            $feed = FeedReader::getCachedFeed($url);
            $cEnclosures = 0;

            foreach ($feed as $entry) {
                $oEnclosure = $entry->getEnclosure();
                if (isset($oEnclosure->url) && !empty($oEnclosure->url)) {
                    $cEnclosures++;
                }
            }

            $feedReader = new FeedReader();
            try {
                $cats = $feed->getItunesCategories();
                if (!$cats || !is_array($cats)) {
                    throw new \Exception();
                }
                $categories = $this->convertItunesCategories($cats);
            } catch (\Exception $e) {
                $categories = [
                    'Leisure'
                ];
            }
            $imageId = null;
            $imageUrl = $feed->getItunesImage() ? $feed->getItunesImage() : ($feed->getPlayPodcastImage() ? $feed->getPlayPodcastImage() : null);
            if ($imageUrl) {
                $mm = new MediaManager(auth()->user());
                try {
                    $res = $mm->saveFileFromUrl($imageUrl);

                    if ($res) {
                        if (!is_logo($res['file'])) {
                            File::delete($res['file']['path']);
                            $imageUrl = null;
                        } else {
                            $imageId = $res['file']['id'];
                        }
                    }
                } catch (\Exception $e) {
                }
            }


            $res = [
                'passed' => true,
                'feed' => [
                    'rss' => [
                        'author' => $feedReader->getName($feed),
                        'email' => $feedReader->getEmail($feed),
                        'authors' => $feed->getAuthors(),
                        'title' => $feed->getTitle(),
                        'description' => strip_tags($feed->getDescription()),
                        'language' => Str::before($feed->getLanguage(), '-'),
                        'copyright' => $feed->getCopyright(),
                        'link' => $feed->getLink(),
                    ],
                    'itunes' => [
                        'category' => $categories,
                    ],
                    'logo' => [
                        'itunes' => $imageId,
                    ],
                    'imageUrl' => $imageUrl
                ],
                'message' => trans('feeds.message_success_import_feed_check', ['count' => $cEnclosures])
            ];
        } catch (\Exception $e) {
        }

        return response()->json($res);
    }

    /**
     * @param  array  $categories
     * @return array
     */
    private function convertItunesCategories(array $categories): array
    {
        $itunes = Datacenter::getItunesCategories();

        $res = [];

        foreach ($categories as $key => $a) {
            if (is_array($a) && count($a) > 0) {
                $sub = array_key_first($a);
                $_cat = trim(htmlentities($key) . ':' . $sub);
                if (!array_key_exists($_cat, $itunes)) {
                    continue;
                }
                $res[] = $_cat;
            } else {
                $_cat = htmlentities(trim($key));

                if (!array_key_exists($_cat, $itunes)) {
                    continue;
                }
                $res[] = $_cat;
            }
        }

        return $res;
    }
}
