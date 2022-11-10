<?php

namespace App\Http\Resources;

use App\Models\Show;
use Illuminate\Support\Facades\Log;

class FeedCollectionResource extends FeedJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $type = 'feed';

        if (isset($this->domain['feed_redirect']) && $this->domain['feed_redirect']) {
            $type = 'redirect';
        }

        $aLinks = [
            "self" => secure_url('api/feeds', ['id' => $this->feed_id]),
//                "first" => "http://example.com/articles?page=1",
//                "last" => "http://example.com/articles?page=2",
//                "prev" => null,
//                "next" => "http://example.com/articles?page=2"
            "rss" => $this->getFeedLink(),
            "web" => $this->getWebsiteLink(),
            'logo' => $this->getLogoLink(),
        ];

        if ($type === 'redirect') {
            $aLinks['rss_original'] = $this->getFeedLink(true);
        }

        if (isset($this->domain['website_redirect'])
            && $this->domain['website_redirect']) {
            $aLinks['web_original'] = $this->getWebsiteLink(true);
        }

        $entries = false;
        if (isset($this->resource->additional['withShows']) && $this->resource->additional['withShows']) {
            $collectionId = false;
            if (isset($this->settings['audiotakes']) && $this->settings['audiotakes'] && isset($this->settings['audiotakes_id']) && $this->settings['audiotakes_id']) {
                $collectionId = $this->settings['audiotakes_id'];
            }
            $entries = collect($this->entries);
            $entries = $entries->map(function($show) use ($collectionId) {
                $_show = new Show($show);
                $_show->title = $show['title'];
                $_show->lastUpdate = $show['lastUpdate'];
                $_show->domain = $this->domain;
                $_show->username = $this->username;
                $_show->feed_id = $this->feed_id;

                if ($collectionId) {
                    if (isset($show['audiotakes_guid']) && $show['audiotakes_guid']) {
                        $_show->episode_id = $collectionId . '-' . $show['audiotakes_guid'];
                    } else {
                        $username = $this->username;
                        Log::debug("User `$username`: No AID found for show `{$show['guid']}` from feed " . $this->feed_id);
                        try {
                            if (isset($show['show_media']) && !empty($show['show_media'])) {
                                $file = get_file($username, $show['show_media']);

                                if ($file) {
                                    $_show->episode_id = $collectionId . '-' . sha1($file['name']);
                                }
                            }
                        } catch (\Exception $e) {
                            Log::error("ERROR: Username `$username`: Could not get file for show `{$show['guid']}`");
                        }
                    }
                }

                return $_show;
            });
            $entries = new EntryCollection($entries);
        }

        if (!$request->has('feed_id')) {
            // Makes feed_id available in entry collection
            $request->merge(['feed_id' => $this->feed_id]);
        }

        $res = [
            'type' => $type,
            'id' => $this->feed_id,
            'links' => $aLinks,
            'attributes' => [
                //'rss' => $this->rss,
                'title' => $this->rss['title'] ?? '',
                'subtitle' => $this->itunes['subtitle'] ?? null,
                'description' => $this->rss['description'] ?? null,
                'logo' => $this->getLogo(),
                $this->mergeWhen($entries, [
                    'shows' => $entries
                ])
            ],
            'shows_count' => /*$this->shows_count*/ $this->shows->count(),
            'relationships' => new FeedRelationshipResource($this),
        ];

        return $res;
    }
}
