<?php

namespace App\Http\Resources\Committee;

use App\User;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class CommitteeData extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    // SQLSTATE[42S22]: Column not found: 1054 Unknown column 'position' in 'field list' (SQL: select `id`, `image`, `firstName`, `lastName`, `position`, `status` from `users` where `committee_id` = 1)

     public function position($pos)
    {
        $volunteer = DB::table('users')->join('volunteers','users.id','=','volunteers.user_id')->join('vol_committees', function ($join) use ($pos) {
            $join->on('volunteers.id', '=', 'vol_committees.vol_id')
                 ->where('vol_committees.position', '=', $pos )->where('season_id',DB::table('seasons')->where('isActive',1)->value('id'))->where('vol_committees.committee_id',$this->id);
        })
        ->select('users.firstName' , 'users.lastName','volunteers.id')->get();
        return $volunteer;
    }
     public function volunteers($committeeId)
    {
        $volunteers = DB::table('users')->join('volunteers','users.id','=','volunteers.user_id')->join('statuses','volunteers.status_id','=','statuses.id')->join('vol_committees', function ($join) use ($committeeId) {
            $join->on('volunteers.id', '=', 'vol_committees.vol_id')
                 ->where('vol_committees.position', '=', 'volunteer' )->where('season_id',DB::table('seasons')->where('isActive',1)->value('id'))->where('vol_committees.committee_id',$committeeId);
        })->select('users.firstName' ,'vol_committees.position', 'users.lastName', 'users.image','volunteers.id','statuses.name')->get();
        return $volunteers;
    }


    public function toArray($request)
    {

        return [
            'id' => $this->id,
            'name' => $this->name,
            'mentor' => self::position('mentor'),
            'director' =>self::position('mentor'),
            'hr_coordinator' => self::position('mentor'),
            'members' =>self::volunteers($this->id)
        ];
    }
}
