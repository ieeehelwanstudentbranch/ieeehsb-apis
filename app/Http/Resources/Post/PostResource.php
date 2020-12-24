<?php

namespace App\Http\Resources\Post;

use App\User;
use App\Volunteer;
use Illuminate\Http\Resources\Json\JsonResource;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Resources\Post\CommentsCollection;


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
                $vol =  Volunteer::findOrFail($this->creator);

        return [
            'id' => $this->id,
            'body' => $this->body,
            'created_at' => $this->created_at->toDateTimeString(),
            'post_owner' => new OwnerCollection($vol),
            'comments'   => CommentsCollection::collection($this->comments),
            'update'    => action('PostController@update' , $this->id ),
            'delete'    => action('PostController@destroy' , $this->id ),


        ];
    }
}
