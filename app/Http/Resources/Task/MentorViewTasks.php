<?php

namespace App\Http\Resources\Task;

use App\Committee;
use App\Task;
use App\User;
use Illuminate\Http\Resources\Json\Resource;
use phpDocumentor\Reflection\Types\Parent_;
use Tymon\JWTAuth\Facades\JWTAuth;

class MentorViewTasks extends Resource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
//        $committee=Committee::where('mentor_id',JWTAuth::parseToken()->authenticate()->id);
////        dd($committee);
//        $committeeTasks = Task::all()->where('committee_id', $committee->id)->where('status','pending');
//
////        $tasksRecived = Task::all()->where('from', JWTAuth::parseToken()->authenticate()->id)->where('status','pending');
        return [
            'committee tasks' =>$this,

//            'personal tasks'  =>$tasksRecived,


        ];
    }
}
