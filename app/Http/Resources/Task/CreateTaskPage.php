<?php

namespace App\Http\Resources\Task;

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
        $EX_com = $this->where('position','EX_com');
        $high_board = $this->where('position','highBoard');
        $volunteer = $this->where('position','volunteer');
        return [
            'EX_com'=>$EX_com,
            'highBoard' =>$high_board,
            'volunteer'=>$volunteer,

        ];
    }
}
