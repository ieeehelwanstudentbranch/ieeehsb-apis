<?php

namespace App\Http\Resources\Task;

use App\Committee;
use App\SendTask;
use App\Task;
use App\User;
use Illuminate\Http\Resources\Json\Resource;
use phpDocumentor\Reflection\Types\Parent_;
use Tymon\JWTAuth\Facades\JWTAuth;

class CreateTaskPage extends Resource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'EX_com'=>$this->where('position','EX_com'),
            'highBoard' =>$this->where('position','highBoard'),
            'committee'=>CommitteesInTask::collection(Committee::all()),
        ];
    }
}
