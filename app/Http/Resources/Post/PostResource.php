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
            'title' => $this->title,
            'body' => $this->body,
            'created_at' => $this->created_at,
            'user_id' => $this->user_id,
            'comments'   => $this->comments ,
            'href'       =>[
                'edit'    => action('PostController@update' , $this->id ),
                'delete'    => action('PostController@destroy' , $this->id ),
            ]

        ];
    }
}
