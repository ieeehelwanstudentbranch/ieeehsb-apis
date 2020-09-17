<?php

namespace App\Http\Resources\Task;

use App\Committee;
use App\Http\Resources\Post\OwnerCollection;
use App\SendTask;
use App\Task;
use App\User;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Facades\DB;
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
    public function info($user)
    {

        {
            $volunteer = DB::table('users')
                ->join('volunteers', 'users.id', '=', 'volunteers.user_id')
                ->join('positions', 'positions.id', '=', 'volunteers.position_id')
                ->where('users.id', $user)
                ->select('users.id', 'users.firstName', 'users.lastName', 'users.email', 'users.image', 'positions.name')->get();
            return $volunteer;
        }
    }

    public function toArray($request)
    {
        try{
            return [
                'id' => $this->id,
                'title' => $this->title,
                'deadline' => $this->deadline,
                'committee' => Committee::select('id','name')->where('id', $this->comm_id)->get(),
                'sender_info' => self::info($this->from),
                'receiver_info' =>  self::info($this->to),
                // 'details' => $this->body_sent,
                // 'files_sent' => $this->files_sent,
                // 'deliver_description' => $this->body_deliver,
                // 'deliver_files' => $this->files_deliver,
                'task_status' =>$this->status->name,
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
                'sender_info' => self::info($this->from),
                'receiver_info' =>  self::info($this->to),
                // 'details' => $this->body_sent,
                // 'files_sent' => $this->files_sent,
                // 'deliver_description' => $this->body_delivered,
                // 'deliver_files' => $this->files_delivered,
                'task_status' =>$this->status->name,
                'task_rate' =>$this->rate,
                'create_at' =>$this->created_at->toDateTimeString() ,
                'deliver_at' =>$this->updated_at->toDateTimeString() ,
            ];
        }

    }
}
