<?php

namespace App\Http\Resources\Post;

use Illuminate\Http\Resources\Json\Resource;

class CommentsCollection extends Resource
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
            'body' => $this->comment_body,
            'created_at' => $this->created_at,
            'delete'    => action('CommentController@destroy' , $this->id ),
            'update'    => action('CommentController@update' , $this->id ),
            'comment_owner' => new OwnerCollection($vol),
        ];
        // return parent::toArray($request);
    }
}
