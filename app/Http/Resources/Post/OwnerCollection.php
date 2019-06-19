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
                'user_id' => $this->id,
                'firstName' => $this->firstName,
                'lasttName' => $this->lastName,
                'image' => $this->image,
                'position' => $this->position,
                'href' =>    action('UserController@index',$this->id)
            ];

    }
}
