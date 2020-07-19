<?php

namespace App\Http\Resources\Chapter;
use App\Chapter;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ChapterCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        $chairpersons = DB::table('volunteers')->join('users','volunteers.user_id','=','users.id')->join('positions','volunteers.position_id','=','positions.id')->join('roles','positions.role_id','=','roles.id')->where('roles.name','ex_com')->select('volunteers.id','users.firstName','users.lastName','positions.name')
        ->get();
         return [
            'chairpersons' =>$chairpersons,

        ];
    }
}
