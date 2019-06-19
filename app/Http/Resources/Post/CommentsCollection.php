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
//        dd($this);
            return [
                'comment_id' => $this->id,
                'comment_body' => $this->comment_body,
                'created_at' => $this->created_at,
                'delete'    => action('CommentController@destroyComment' , $this->id ),
                'comment_owner' => new OwnerCollection($this->user),
            ];
//        return parent::toArray($request);

    }
}
