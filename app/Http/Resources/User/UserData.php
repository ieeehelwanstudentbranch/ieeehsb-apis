<?php

namespace App\Http\Resources\User;

use App\Status;
use App\Position;
use App\Committee;
use App\Volunteer;
use App\Role;
use Illuminate\Http\Resources\Json\JsonResource;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\DB;

class UserData extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

      if(Volunteer::where('user_id',$this->id)->first() != null)
      {
      $vol = Volunteer::where('user_id',$this->id)->first();
      $role = $vol->position->role->name;
      $status =$vol->status->name;
      $volCom = DB::table('vol_committees')->where('vol_id',$vol->id)->value('committee_id');
      $volPos= DB::table('vol_committees')->where('vol_id',$vol->id)->value('position');

            $data = [
                'id' => $this->id,
                'firstName' => $this->firstName,
                'lastName' => $this->lastName,
                'email' => $this->email,
                'faculty' => $this->faculty,
                'university' => $this->university,
                'DOB' => $this->DOB,
                'address' => $this->address,
                'phone' => $this->phone,
                'level' => $this->level,
                'image' => $this->image,
                'status' => $status,
                'role' =>$role,
                'position' => $vol->position->name,
                'committee' => $role != "ex_com"? Committee::query()->findOrFail($volCom)->value('name') :null ,
                'created_at' => $this->created_at->toDateTimeString(),

            ];

            //if the volunteer is not logged in
            if($this->id != JWTAuth::parseToken()->authenticate()->id) {
                return  [
                  $data
            ]; }
            //if the volunteer logged in
            else {
              return [$data,
              'update' => action('UserController@update', $this->id),
              'update_image' => action('UserController@updateProfileImage', $this->id),
              'update_password' => action('UserController@updateProfilePassword', $this->id)
            ]; }
        }
        //is a participant
        else {
            $data =  [
                'id' => $this->id,
                'firstName' => $this->firstName,
                'lastName' => $this->lastName,
                'email' => $this->email,
                'faculty' => $this->faculty,
                'university' => $this->university,
                'DOB' => $this->DOB,
                'address' => $this->address,
                'phone' => $this->phone,
                'level' => $this->level,
                'image' => $this->image,
                'created_at' => $this->created_at->toDateTimeString(),
            ];
            //if the participant is not logged in
            if($this->id != JWTAuth::parseToken()->authenticate()->id) {
                return  [
                  $data
            ]; }
            else {
              //if the volunteer is logged in
              return [$data,
              'update' => action('UserController@update', $this->id),
              'update_image' => action('UserController@updateProfileImage', $this->id),
              'update_password' => action('UserController@updateProfilePassword', $this->id)
            ]; }
          }
  }
}
