<?php

namespace App\Http\Resources;

use Carbon\CarbonImmutable;
use Illuminate\Http\Resources\Json\JsonResource;

class ShowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $feedId = $request->feed_id;

        return [
            'type' => 'show',
            'id' => $this->guid,
            'feed_id' => $feedId,
            'attributes' => [
                'guid' => $this->guid,
                'title' => $this->title ?? '',
                'subtitle' => $this->itunes['subtitle'] ?? null,
                'link' => $this->link ?? '',
                'description' => $this->description ?? '',
                'author' => $this->author ?? '',
                'email' => $this->email ?? '',
                'copyright' => $this->copyright ?? '',
                'itunes' => $this->itunes,
                //'google' => $this->googleplay,
                //'logo' => $this->getLogo(),
                'show_media' => $this->show_media ?? null,
                'is_public' => $this->is_public,
                'publishing_date' => CarbonImmutable::createFromTimestamp($this->lastUpdate)->format('Y-m-d'),
                'publishing_time' => CarbonImmutable::createFromTimestamp($this->lastUpdate)->format('H:i:s'),
            ],
            //'links' => $aLinks,
            //'relationships' => new FeedRelationshipResource($this),
        ];
    }
}
