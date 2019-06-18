<?php

namespace App\Http\Resources\Post;

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
            return [
                'body' => $this->body,
                'created_at' => $this->created_at,
                'user_id' => $this->user_id,
                'href' =>    action('PostController@show',$this->id)
            ];

    }
}
