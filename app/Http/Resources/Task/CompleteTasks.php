<?php

namespace App\Http\Resources\Task;

use App\Committee;
use App\DeliverTask;
use App\Task;
use Illuminate\Http\Resources\Json\Resource;
use phpDocumentor\Reflection\Types\Parent_;
use Tymon\JWTAuth\Facades\JWTAuth;

class CompleteTasks extends Resource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $committees_mentor= Committee::query()->where('mentor_id',JWTAuth::parseToken()->authenticate()->id)->get();

        $committees_hr_od= Committee::query()->where('hr_coordinator_id',JWTAuth::parseToken()->authenticate()->id)->get();

        $tasksSent = Task::all()->where('from', JWTAuth::parseToken()->authenticate()->id)->where('status','accepted');
        $tasksRecived = Task::all()->where('to', JWTAuth::parseToken()->authenticate()->id)->where('status','accepted');

        try
        {
            foreach ($committees_mentor as $committee)
            {
                $committeeTasks[] = Task::where('committee_id', $committee->id)->where('status', 'accepted')->get();
            }

            return [
                'mentoring_tasks' =>$committeeTasks,
                'committee_tasks' =>$tasksSent,
                'personal_tasks'  =>$tasksRecived,
            ];
        }catch (\Exception $e){
            try{
                foreach ($committees_hr_od as $committee)
                {
                    $committeeTask[] = Task::where('committee_id', $committee->id)->where('status', 'accepted')->get();
                }
                return [
                    'hr_tasks' =>$committeeTask,
                    'committee_tasks' =>$tasksSent,
                    'personal_tasks'  =>$tasksRecived
                ];

            }catch (\Exception $e){
            return [
                'committee_tasks' =>$tasksSent,
                'personal_tasks'  =>$tasksRecived,
            ];
        }
        }

    }
}
