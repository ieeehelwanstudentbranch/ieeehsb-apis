<?php

namespace App\Http\Resources\Post;

use App\Committee;
use Illuminate\Http\Resources\Json\Resource;

class RegisterCollection extends Resource
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
            'committees' =>Committee::query()->select('id','name')->get(),
        ];
    }
}
