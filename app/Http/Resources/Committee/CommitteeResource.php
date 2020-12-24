<?php

namespace App\Http\Resources\Committee;

use App\Committee;
use App\User;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Facades\DB;

class CommitteeResource extends Resource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function position($pos)
    {
        $volunteer = DB::table('users')->join('volunteers','users.id','=','volunteers.user_id')->join('positions','positions.id','=','volunteers.position_id')->join('vol_history', function ($join) use ($pos) {
            $join->on('volunteers.id', '=', 'vol_history.vol_id')
                 ->where('vol_history.position_id', '=', DB::table('positions')->where('name',$pos)->value('id'))->where('season_id',DB::table('seasons')->where('isActive',1)->value('id'));
        })
        ->select('users.firstName' , 'users.lastName','positions.name','volunteers.id')->get();
        return $volunteer;
    }
    public function toArray($request)
    {
        try {
            return [
                'mentor' => self::position('mentor'),
                'director' =>self::position('director'),
                'hr-od' =>self::position('hr_od'),
            ];
        }catch (\Exception $e){
            return [
                'mentor' => self::position('mentor'),
                'director' =>self::position('director'),
                'hr-od' =>self::position('hr_od'),
            ];
        }
    }
}
