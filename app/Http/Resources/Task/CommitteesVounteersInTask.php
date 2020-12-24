<?php

namespace App\Http\Resources\Task;

use App\Committee;
use App\SendTask;
use App\Task;
use App\User;
use App\Volunteer;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\Types\Parent_;
use Tymon\JWTAuth\Facades\JWTAuth;

class CommitteesVounteersInTask extends Resource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $vol = Volunteer::findOrFail($this->vol_id);

        return [
            'id'=>$this->vol_id,
            'name'=>$vol->user->firstName.' '.$vol->user->lastName,
            'position' => $this->position,
        ];
    }
}
