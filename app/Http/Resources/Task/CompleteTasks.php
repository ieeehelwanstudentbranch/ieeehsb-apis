<?php

namespace App\Http\Resources\Task;

use App\DeliverTask;
use Illuminate\Http\Resources\Json\Resource;
use phpDocumentor\Reflection\Types\Parent_;
use Tymon\JWTAuth\Facades\JWTAuth;

class CompleteTasks extends Resource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $tasksSent = DeliverTask::all()->where('to', JWTAuth::parseToken()->authenticate()->id)->where('status','accepted');
        $tasksRecived = DeliverTask::all()->where('from', JWTAuth::parseToken()->authenticate()->id)->where('status','accepted');
        return [
            'committee tasks'       =>$tasksSent,
            'personal tasks'       => $tasksRecived,



        ];

    }
}
