<?php

namespace App\Http\Resources\Task;

use App\SendTask;
use App\Task;
use App\User;
use Illuminate\Http\Resources\Json\Resource;
use phpDocumentor\Reflection\Types\Parent_;
use Tymon\JWTAuth\Facades\JWTAuth;

class TaskCollectionPanding extends Resource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if ($this->from == JWTAuth::parseToken()->authenticate()->id){
        return [

            'task name' => $this->title,
            'task deadline' => $this->deadline,
//            'task from' => User::select('id','firstName','lastName' , 'position','email')->where('id', $this->from)->get(),
            'task to' => User::select('id','firstName','lastName' , 'position','email')->where('id', $this->to)->get(),
            'task details' => $this->body_sent,
            'task files sent' => $this->files_sent,
            'task deliver description' => $this->body_deliver,
            'task deliver files' => $this->files_deliver,
            'deliver at' =>$this->updated_at ,
            'create at' =>$this->created_at ,
            'href' => [
             'accept' => action('TaskController@acceptTask',$this->id),
             'refuse' => action('TaskController@refuseTask',$this->id),
            ],
        ];
        }elseif($this->to == JWTAuth::parseToken()->authenticate()->id){

            return [

                'task name' => $this->title,
                'task deadline' => $this->deadline,
                'task from' => User::select('id','firstName','lastName' , 'position','email')->where('id', $this->from)->get(),
                'task details' => $this->body_sent,
                'task files sent' => $this->files_sent,
                'href' => [
                    'deliver' => action('TaskController@deliverTask',$this->id)
                ],
            ];
        }else{
            return 'dssds';
        }
    }
}
