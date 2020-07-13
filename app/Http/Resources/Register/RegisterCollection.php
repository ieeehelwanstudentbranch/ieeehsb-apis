<?php

namespace App\Http\Resources\Post;

use App\Committee;
use App\Role;
use App\Position;
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
            'roles' => Role::query()->select('id','name')->get(),
            'positions' => Position::query()->select('id','name')->get(),
            'chapters' => Chapter::query()->select('id','name')->get(),
        ];
    }
}
