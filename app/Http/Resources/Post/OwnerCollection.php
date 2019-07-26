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
                'id' => $this->id,
                'firstName' => $this->firstName,
                'lastName' => $this->lastName,
                'image' => $this->image,
                'position' => $this->position,
            ];

    }
}
