<?php

namespace App\Http\Resources\Committee;

use App\Committee;
use App\User;
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
        $numOfVolunteers = User::where('committee_id',$this->id)->where('position','volunteer')->get();
        return [
            'id' => $this->id,
            'name' => $this->name,
            'mentor' => $this->mentor,
            'mentor_id' => $this->mentor_id,
            'director' => $this->director,
            'director_id' => $this->director_id,
            'hr_coordinator' => $this->hr_coordinator,
            'hr_coordinator_id' => $this->hr_coordinator_id,
            'numOfVolunteers' => $numOfVolunteers->count(),
        ];
    }
}
