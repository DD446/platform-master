<?php

namespace App\Classes;

use App\Models\Feed;

class PodcastLinkService {

    public function save(Feed $feed, string $type, ?string $link = null): bool
    {
        \Illuminate\Support\Facades\Log::debug('User ' . auth()->user()->username . ': Saving submit link ' . $link . ' for feed ' . $feed->feed_id . ' for user ' . $feed->username);

        $aLinks = $feed->submit_links ?? [];

        if (!$link) {
            if (array_key_exists($type, $aLinks)) {
                unset($aLinks[$type]);
            }
        } else {
            $aLinks[$type] = $link;
        }

        return $feed->whereUsername($feed->username)
        ->whereFeedId($feed->feed_id)
        ->update(['submit_links' => $aLinks]);
    }
}
