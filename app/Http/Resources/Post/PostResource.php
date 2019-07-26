<?php

namespace App\Http\Resources\Post;

use App\User;
use Illuminate\Http\Resources\Json\JsonResource;
use Tymon\JWTAuth\Facades\JWTAuth;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'body' => $this->body,
            'created_at' => $this->created_at->toDateTimeString(),
            'post_owner' => new OwnerCollection($this->user),
//            'comments'   => CommentsCollection::collection($this->comments),
        ];
    }
}
