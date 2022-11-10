<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FaqCollectionResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'link' => route('faq.show', ['id' => $this->id, 'slug' => $this->slug]),
            'attributes' => [
                'id' => $this->id,
                'question' => $this->question,
                'answer' => $this->answer,
                'last_updated' => $this->last_updated,
                'category' => trans_choice('faq.categories', $this->category_id),
            ]
        ];
    }
}
