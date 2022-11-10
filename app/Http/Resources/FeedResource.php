<?php

namespace App\Http\Resources;

class FeedResource extends FeedJsonResource
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

        if (isset($this->domain['website_redirect']) && $this->domain['website_redirect']) {
            $aLinks['web_original'] = $this->getWebsiteLink(true);
        }

        //return parent::toArray($request);
        return [
            'type' => $type,
            'id' => $this->feed_id,
            'attributes' => [
                //'rss' => $this->rss,
                'title' => $this->rss['title'] ?? '',
                'subtitle' => $this->itunes['subtitle'] ?? null,
                'link' => $this->rss['link'] ?? '',
                'description' => $this->rss['description'] ?? '',
                'author' => $this->rss['author'] ?? '',
                'email' => $this->rss['email'] ?? '',
                'copyright' => $this->rss['copyright'] ?? '',
                'language' => $this->rss['language'] ?? '',
                'category' => $this->rss['category'] ?? '',
                'rss' => $this->rss,
                'itunes' => $this->itunes,
                'googleplay' => $this->googleplay,
                //'entries' => $this->entries,
                'logo' => $this->getLogo(),
            ],
            'links' => $aLinks,
            'shows_count' => /*$this->shows_count*/ $this->shows->count(),
            'relationships' => new FeedRelationshipResource($this),
            $this->mergeWhen(isset($this->settings['is_importing']) && $this->settings['is_importing'], [
                'is_importing' => true,
            ])
        ];
    }
}
