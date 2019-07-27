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
        return [
            'id' => $this->id,
            'body' => $this->comment_body,
            'created_at' => $this->created_at,
            'delete'    => action('CommentController@destroyComment' , $this->id ),
            'update'    => action('CommentController@updateComment' , $this->id ),
            'comment_owner' => new OwnerCollection($this->user),
        ];
        // return parent::toArray($request);
    }
}
