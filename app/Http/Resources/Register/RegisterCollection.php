<?php

namespace App\Http\Resources\Register;

use Illuminate\Http\Resources\Json\ResourceCollection;

class RegisterCollection extends ResourceCollection
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
             'positions' => Position::query()->where('role_id',Role::where('name','ex_com')->value('id'))->select('id','name')->get(),
             'chapters' => Chapter::query()->select('id','name')->get(),
         ];
     }
}
