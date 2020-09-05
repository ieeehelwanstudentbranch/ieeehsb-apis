<?php

namespace App\Http\Resources\Chapter;
use App\Chapter;
use App\Role;
use App\Volunteer;
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
     public function toArray($request)
    {

        // $chairpersons = DB::table('volunteers')->join('users','volunteers.user_id','=','users.id')->where('volunteers.position_id',DB::table('positions')->where('name','LIKE','%'. $this->name .'%')->value('id'))->where('status_id',DB::table('statuses')->where('name','activated')->value('id'))->select('users.firstName','users.lastName','volunteers.id')->get();

        $chairperson = DB::table('volunteers')
            ->join('users','volunteers.user_id','=','users.id')

            ->join('positions','volunteers.position_id','=','positions.id')
            ->where('volunteers.id' ,'=' ,$this->chairperson_id)
            ->select('volunteers.id','users.firstName','users.lastName','positions.name')
        ->get();
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'logo'=> $this->logo,
            'chairperson' => $chairperson,
            'committees' => $this->committee,


        ];
    }
}
