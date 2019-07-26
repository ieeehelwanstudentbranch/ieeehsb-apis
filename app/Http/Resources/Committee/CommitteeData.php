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
            'mentor' => $this->mentor,
            'director' => $this->director,
            'hr_coordinator' => $this->hr_coordinator,
            'members'   =>User::query()->select('id', 'image', 'firstName', 'lastName', 'position', 'status')->where('committee_id' ,$this->id)->get()
        ];
    }
}
