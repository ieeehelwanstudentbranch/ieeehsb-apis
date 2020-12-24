<?php

namespace App\Http\Resources\Task;

use App\Committee;
use App\Position;
use App\SendTask;
use App\Task;
use App\Role;
use App\User;
use App\Volunteer;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Facades\DB;
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
    public function position($pos)
    {
        $volunteer = DB::table('users')
            ->join('volunteers','users.id','=','volunteers.user_id')
            ->join('positions','positions.id','=','volunteers.position_id')
            ->join('roles', function ($join) use ($pos) {
            $join->on('roles.id', '=', 'positions.role_id')
                ->where('roles.name', '=', $pos );
        })
            ->select('users.firstName' , 'users.lastName','volunteers.id','positions.name')->get();
        return $volunteer;
    }
    public function toArray($request)
    {

        return [
            'ex_com'=>self::position('ex_com'),
            'highBoard' =>self::position('highboard'),
            'committee'=>CommitteesInTask::collection(Committee::all()),
        ];
    }
}
