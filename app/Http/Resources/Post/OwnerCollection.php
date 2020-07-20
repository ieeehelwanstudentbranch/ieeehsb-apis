<?php

namespace App\Http\Resources\Post;

use Illuminate\Http\Resources\Json\Resource;

class OwnerCollection extends Resource
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
                'id' => $this->user->id,
                'firstName' => $this->user->firstName,
                'lastName' => $this->user->lastName,
                'image' => $this->user->image,
                'position' => $this->position->name,
            ];

    }
}
