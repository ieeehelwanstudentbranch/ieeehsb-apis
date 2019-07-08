<?php

namespace App\Http\Resources\User;

use App\Committee;
use App\Ex_com_options;
use Illuminate\Http\Resources\Json\JsonResource;
use Tymon\JWTAuth\Facades\JWTAuth;

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
        if($this->id == JWTAuth::parseToken()->authenticate()->id) {
            return [
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
                'status' => $this->status,
                'image' => $this->image,
                'position' => $this->position,
                'ex_options' => $this->position ? Ex_com_options::query()->where('user_id', $this->id)->select('ex_options')->get() : null,
                'created_at' => $this->created_at->toDateTimeString(),
                'committee' => $this->committee_id ? Committee::query()->findOrFail($this->committee_id) : null,
                'update' => action('UserController@updateProfile', $this->id),
                'update_image' => action('UserController@updateProfileImage', $this->id),
                'update_password' => action('UserController@updateProfilePassword', $this->id)
            ];
        } else {
            return [
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
                'status' => $this->status,
                'image' => $this->image,
                'position' => $this->position,
                'ex_options' => $this->position ? Ex_com_options::query()->where('user_id', $this->id)->select('ex_options')->get() : null,
                'created_at' => $this->created_at->toDateTimeString(),
                'committee' => $this->committee_id ? Committee::query()->findOrFail($this->committee_id) : null
            ];
        }
    }
}
