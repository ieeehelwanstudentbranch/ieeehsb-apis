<?php

namespace App\Http\Resources\Committee;

use App\Committee;
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
        try {
            return [
                'mentor' =>User::select('id','firstName','lastName' , 'position')->where('position', 'EX_com')->get(),
                'director' =>User::select('id','firstName','lastName' , 'position' ,'committee_id')->where('position', 'highBoard')->get(),
                'hr-od' =>User::select('id','firstName','lastName' ,'position')->where('committee_id', $this->id)->get(),
            ];
        }catch (\Exception $e){
            return [
                'mentor' =>User::select('id','firstName','lastName' , 'position')->where('position', 'EX_com')->get(),
                'director' =>User::select('id','firstName','lastName' , 'position' ,'committee_id')->where('position', 'highBoard')->get(),
            ];
        }
    }
}
