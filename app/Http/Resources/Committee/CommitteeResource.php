<?php

namespace App\Http\Resources\Committee;

use App\User;
use Illuminate\Http\Resources\Json\Resource;

class CommitteeResource extends Resource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
      $users=  User::all()->where('committee_id', $this->id);
        return [
            'director' =>User::select('firstName','lastName' , 'position')->where('position', 'highBoard')->get(),

            'hr-od' =>User::select('firstName','lastName' ,'position')->where('committee_id', $this->id)->get(),
        ];
    }
}
