<?php

namespace App\Http\Resources\Committee;

use App\User;
use Illuminate\Http\Resources\Json\JsonResource;

class CommitteeData extends JsonResource
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
            'id' => $this->id,
            'name' => $this->name,
            'mentor' => $this->Ex_com_Mentor,
            'director' => $this->director,
            'hr_ordinator' => $this->hr_ordinator,
            'members'      =>User::where('committee_id' ,$this->id)->get(),

//            'href'       =>[
//                'members'      =>[User::where('committee_id' ,$this->id)->get(),
//                'view user'   =>   action('CommitteeController@viewUser',User::where('committee_id', $this->id)->pluck('id')),
//
//            ]]
        ];
    }
}
