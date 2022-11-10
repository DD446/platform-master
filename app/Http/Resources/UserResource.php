<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        //return parent::toArray($request);
        return [
            'type' => 'user',
            'id' => $this->id,
            'attributes' => [
                'id' => $this->id,
                'name' => $this->name,
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'username' => $this->username,
                'nickname' => $this->nickname,
                'email' => $this->email,
                'avatar' => $this->avatar,
            ],
            'links' => [
                "self" => secure_url('api/user'),
            ],
            //'relationships' => new UserRelationshipResource($this),
        ];
    }
}
