<?php

namespace App\Http\Resources\Task;

use App\DeliverTask;
use App\Task;
use Illuminate\Http\Resources\Json\Resource;
use phpDocumentor\Reflection\Types\Parent_;
use Tymon\JWTAuth\Facades\JWTAuth;

class PendingTasks extends Resource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $tasksSent = Task::all()->where('to', JWTAuth::parseToken()->authenticate()->id)->where('status','pending');
        $tasksRecived = Task::all()->where('from', JWTAuth::parseToken()->authenticate()->id)->where('status','pending');
        return [
            'committee tasks' =>$tasksSent,

            'personal tasks'  =>$tasksRecived,


        ];


    }
}
