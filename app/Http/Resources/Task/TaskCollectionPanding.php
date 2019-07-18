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
                'title' => $this->title,
                'deadline' => $this->deadline,
                'sender_info' => User::select('id', 'image', 'firstName', 'lastName', 'position', 'email')->where('id', $this->from)->get(),                'receiver_info' => User::select('id','firstName','lastName' , 'position','email')->where('id', $this->to)->get(),
                'receiver_info' => User::select('id', 'image', 'firstName', 'lastName', 'position', 'email')->where('id', $this->to)->get(),
                'details' => $this->body_sent,
                'sent_files' => $this->files_sent,
                'delivered_details' => $this->body_deliver,
                'delivered_files' => $this->files_deliver,
                'delivered_at' =>$this->updated_at,
                'created_at' =>$this->created_at,
            ];
        }elseif($this->to == JWTAuth::parseToken()->authenticate()->id){
            return [
                'title' => $this->title,
                'deadline' => $this->deadline,
                'created_at' =>$this->created_at,
                'receiver_info' => User::select('id', 'image', 'firstName', 'lastName', 'position', 'email')->where('id', $this->to)->get(),
                'sender_info' => User::select('id', 'image', 'firstName', 'lastName', 'position', 'email')->where('id', $this->from)->get(),
                'details' => $this->body_sent,
                'sent_files' => $this->files_sent
            ];
        }else{
            return 'error';
        }
    }
}
