<?php

namespace App\Http\Resources\Committee;

use Illuminate\Http\Resources\Json\Resource;

class CommitteeCollection extends Resource
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
            'name' => $this->name,
            'mentor' => $this->Ex_com_Mentor,
            'director' => $this->director,
            'hr_ordinator' => $this->hr_ordinator,
            'href'       =>[
                'view Committee'   =>   action('CommitteeController@view',$this->id),
            ]
        ];
    }
}
