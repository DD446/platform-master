<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class EntryCollectionResource extends FeedJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $feedId = $request->feed_id ?? $this->additional['feed_id'];
        $logoLink = $this->getShowImageLink($feedId, $this->domain);
        // TODO: Prüfen, ob das auch geht: , config('app.timezone')
        $date = Carbon::createFromTimestamp($this->lastUpdate, 'Europe/Berlin');
        $mediaLink = $this->getShowMediaLink($feedId, $this->domain);
        $selfLink = url('api/shows', ['guid' => $this->guid],  true) . '?feedId=' . $feedId;

        return [
            'type' => 'show',
            'id' => $this->guid,
            'feed_id' => $feedId,
            'links' => [
                "self" => $selfLink,
//                "first" => "http://example.com/articles?page=1",
//                "last" => "http://example.com/articles?page=2",
//                "prev" => null,
//                "next" => "http://example.com/articles?page=2"
                //"rss" =>  rtrim($this->domain['hostname'], '/') . '/' . $this->feed_id . '.rss',
                "web" => $this->link,
                "logo" => $logoLink,
                "media" => $mediaLink,
            ],
            'attributes' => [
                'title' => $this->title,
                'description' => $this->description,
                'author' => $this->author,
                'link' => $this->link,
                'copyright' => $this->copyright,
                'logo' => $logoLink,
                'guid' => $this->guid,
                'episode_id' => $this->episode_id ?? null,
                'publish_date' => $this->lastUpdate,
                'publish_date_formatted' => $date->format(trans('shows.date_formatted_format')),
                'is_published' => $this->is_public,
                'file' => $this->getShowMedia(),
                'enclosure_url' => $mediaLink,
                'itunes' => $this->itunes,
                'type' => $this->getType($mediaLink),
                'duration_formatted' => isset($this->itunes['duration']) ? $this->getDurationFormatted($this->itunes['duration']) : null,
            ],
            'relationships' => new EntryRelationshipResource($this),
        ];
    }
}
