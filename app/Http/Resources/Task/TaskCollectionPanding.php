<?php

namespace App\Http\Resources\Task;

use App\SendTask;
use App\Status;
use App\Task;
use App\User;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Facades\DB;
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
    public function info($user)
    {
        $volunteer = DB::table('users')
            ->join('volunteers','users.id','=','volunteers.user_id')
            ->join('positions','positions.id','=','volunteers.position_id')
            ->where('users.id', $user)
            ->select('users.id','users.firstName','users.lastName','users.email','users.image','positions.name')->get();
        return $volunteer;
    }
    public function toArray($request)
    {

        if ($this->from == JWTAuth::parseToken()->authenticate()->id){
            return [

                'title' => $this->title,
                'deadline' => $this->deadline,
                'sender_info' => self::info($this->from),
                'receiver_info' => self::info($this->to),
                'details' => $this->body_sent,
                'sent_files' => json_decode($this->files_sent),
                'delivered_details' => $this->body_deliver,
                'delivered_files' => json_decode($this->files_deliver),
                'rate' =>$this->rate,
                'evaluation' =>$this->evaluation,
                'delivered_at' =>$this->updated_at,
                'created_at' =>$this->created_at,
                'status' => $this->status->name,
            ];
        }elseif($this->to == JWTAuth::parseToken()->authenticate()->id){
            return [
                'title' => $this->title,
                'deadline' => $this->deadline,
                'sender_info' => self::info($this->from),
                'receiver_info' => self::info($this->to),
                'details' => $this->body_sent,
                'sent_files' => json_decode($this->files_sent),
                'delivered_details' => $this->body_delivered,
                'delivered_files' => json_decode($this->files_delivered),
                'rate' =>$this->rate,
                'evaluation' =>$this->evaluation,
                'delivered_at' =>$this->updated_at,
                'created_at' =>$this->created_at,
                'status' => $this->status->name,
            ];
        }else{
            return ['error'=>'You are not authorized to access task.'];
        }
    }
}
