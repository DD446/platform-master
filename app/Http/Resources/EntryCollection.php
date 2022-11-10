<?php

namespace App\Http\Resources;

use App\Models\Feed;
use Illuminate\Http\Resources\Json\ResourceCollection;

class EntryCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $a = [
            // !!! This enforces the limited view of the feeds without giving too much details on the internals !!!
            'data' => EntryCollectionResource::collection($this->collection),
        ];

        $item = $this->collection->first();

        if ($item) {
            $username = $item->username;

            if (isset($request->feed_id) || isset($this->additional['feed_id'])) {
                $feedId = $request->feed_id ?? $this->additional['feed_id'];
                if ($username) {
                    $a['total_shows'] = count(Feed::whereUsername($username)->whereFeedId($feedId)->first()->entries);
                }
            }
        } else {
            $a['total_shows'] = 0;
        }

        return $a;
    }
}
