<?php

namespace App\Http\Resources\Chapter;
use App\Chapter;
use App\Role;
use App\Volunteer;
use App\User;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Resources\Json\JsonResource;

class ChapterResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function committee($chId)
    {
        $chapter = Chapter::find($chId);
        $committees = $chapter->committee;
        $data = array();
        foreach ($committees as $committee)
        {
            $numOfVolunteers = DB::table('vol_committees')->where('committee_id',$committee->id)
                ->where('vol_committees.position', '=', 'volunteer')
                ->where('vol_committees.season_id',DB::table('seasons')
                    ->where('isActive',1)->value('id'))->get();
            $data[$committee->id]['id'] = $committee->id;
            $data[$committee->id]['name'] = $committee->name;
            $data[$committee->id]['description'] = $committee->description;
            $data[$committee->id]['numOfVolunteers'] = $numOfVolunteers->count();
            $data[$committee->id]['director'] =  self::position($committee->id);
        }
        return $data;
    }
    public function position( $commId)
    {
        $user = DB::table('volunteers')
            ->join('users','volunteers.user_id','=','users.id')
            ->join('vol_committees','volunteers.id','=','vol_committees.vol_id')->where('committee_id',$commId)
            ->where('vol_committees.position', '=', 'mentor')
            ->where('vol_committees.season_id',DB::table('seasons')
                ->where('isActive',1)->value('id'))->select('users.firstName','lastName')->first();
        return $user;
    }
     public function toArray($request)
    {

        // $chairpersons = DB::table('volunteers')->join('users','volunteers.user_id','=','users.id')->where('volunteers.position_id',DB::table('positions')->where('name','LIKE','%'. $this->name .'%')->value('id'))->where('status_id',DB::table('statuses')->where('name','activated')->value('id'))->select('users.firstName','users.lastName','volunteers.id')->get();
        if ($this->chairperson_id != null) {
            $chairperson = DB::table('volunteers')
                ->join('users', 'volunteers.user_id', '=', 'users.id')
                ->join('positions', 'volunteers.position_id', '=', 'positions.id')
                ->where('volunteers.id', '=', $this->chairperson_id)
                ->select('volunteers.id', 'users.firstName', 'users.lastName', 'positions.name')
                ->get()->first();
        }
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'logo'=> $this->logo,
            'chairperson' => $this->chairperson_id != null ? $chairperson : null,
            'committees' => self::committee($this->id),
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString()


        ];
    }
}
