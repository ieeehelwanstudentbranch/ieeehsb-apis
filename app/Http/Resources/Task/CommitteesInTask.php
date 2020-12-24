<?php

namespace App\Http\Resources\Task;

use App\Committee;
use App\Season;
use App\SendTask;
use App\Task;
use App\User;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\Types\Parent_;
use Tymon\JWTAuth\Facades\JWTAuth;

class CommitteesInTask extends Resource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $volComm = DB::table('vol_committees')->where('committee_id',$this->id)
            ->where('season_id',Season::where('isActive',1)->value('id'))->get();

        return [
            'id'=>$this->id,
            'name'=>$this->name,
            'volunteers'=>CommitteesVounteersInTask::collection($volComm)
        ];
    }
}
