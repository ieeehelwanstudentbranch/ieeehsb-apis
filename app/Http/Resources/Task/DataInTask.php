<?php

namespace App\Http\Resources\Task;

use App\Committee;
use App\Http\Resources\Post\OwnerCollection;
use App\SendTask;
use App\Task;
use App\User;
use Illuminate\Http\Resources\Json\Resource;
use phpDocumentor\Reflection\Types\Parent_;
use Tymon\JWTAuth\Facades\JWTAuth;

class DataInTask extends Resource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        try{
            return [
                'id' => $this->id,
                'title' => $this->title,
                'deadline' => $this->deadline,
                'committee' => Committee::select('id','name')->where('id', $this->committee_id)->get(),
                'sender_info' => User::select('id','firstName','lastName' , 'position','email')->where('id', $this->from)->get(),
                'receiver_info' => User::select('id','firstName','lastName' , 'position','email')->where('id', $this->to)->get(),
                // 'details' => $this->body_sent,
                // 'files_sent' => $this->files_sent,
                // 'deliver_description' => $this->body_deliver,
                // 'deliver_files' => $this->files_deliver,
                'task_status' =>$this->status,
                'task_rate' =>$this->rate,
                'create_at' =>$this->created_at->toDateTimeString() ,
                'deliver_at' =>$this->updated_at->toDateTimeString() ,
            ];
        } catch (\Exception $e){
            return [
                'task_id' => $this->id,
                'title' => $this->title,
                'deadline' => $this->deadline,
                'committee' => Null,
                'sender_info' => User::select('id', 'firstName', 'lastName', 'position', 'email')->where('id', $this->from)->get(),
                'receiver_info' => User::select('id', 'firstName', 'lastName', 'position', 'email')->where('id', $this->to)->get(),
                // 'details' => $this->body_sent,
                // 'files_sent' => $this->files_sent,
                // 'deliver_description' => $this->body_deliver,
                // 'deliver_files' => $this->files_deliver,
                'task_status' =>$this->status,
                'task_rate' =>$this->rate,
                'create_at' =>$this->created_at->toDateTimeString() ,
                'deliver_at' =>$this->updated_at->toDateTimeString() ,
            ];
        }

    }
}
