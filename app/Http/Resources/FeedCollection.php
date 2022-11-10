<?php

namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\ResourceCollection;

class FeedCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @todo look at https://packagist.org/packages/spatie/laravel-fractal if it useful here
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        foreach($this->collection->all() as &$item) {
            $additional = $this->additional;
            $additional['feed_id'] = $item->feed_id;
            $item->additional($additional);
        }
        return [
            // !!! This enforces the limited view of the feeds without giving too much details on the internals !!!
            'data' => FeedCollectionResource::collection($this->collection),
        ];
    }
}
