<?php

namespace App\Http\Resources\Post;
use App\Volunteer;
use App\User;
use Illuminate\Http\Resources\Json\Resource;

class PostCollection extends Resource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $vol =  Volunteer::findOrFail($this->creator);

        return [
            'id' => $this->id,
            'body' => $this->body,
            'created_at' => $this->created_at->toDateTimeString(),
            'post_owner' => new OwnerCollection($vol),
        ];
    }
}
