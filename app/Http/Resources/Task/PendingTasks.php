<?php

namespace App\Http\Resources\Task;

use App\Committee;
use App\DeliverTask;
use App\Task;
use Illuminate\Http\Resources\Json\Resource;
use phpDocumentor\Reflection\Types\Parent_;
use Tymon\JWTAuth\Facades\JWTAuth;

class PendingTasks extends Resource
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

        $committees_hr_od= Committee::all()->where('hr_coordinator_id',JWTAuth::parseToken()->authenticate()->id);

        $tasksSent = Task::where('from', JWTAuth::parseToken()->authenticate()->id)->where('status','pending')->get();
        $tasksRecived = Task::where('to', JWTAuth::parseToken()->authenticate()->id)->where('status','pending')->get();

        try
        {
            foreach ($committees_mentor as $committee)
            {
                $committeeTasks[] = Task::where('committee_id', $committee->id)->where('status', 'pending')->get();
            }

            return [
                'mentoring_tasks' =>DataInTask::collection($committeeTasks),
                'committee_tasks' =>DataInTask::collection($tasksSent),
                'personal_tasks'  =>DataInTask::collection($tasksRecived),
            ];
        }catch (\Exception $e){
            try{
                foreach ($committees_hr_od as $committee)
                {
                    $committeeTask[] = Task::query()->where('committee_id', $committee->id)->where('status', 'pending');
//                    dd($committeeTask);
                }

                return [
                    'hr_coordinating_tasks' =>  DataInTask::collection($committeeTask),
//                    'committee_tasks' =>DataInTask::collection($tasksSent),
//                    'personal_tasks'  =>DataInTask::collection($tasksRecived),
                ];

            }catch (\Exception $e){
                return [
                    'committee_tasks' => DataInTask::collection($tasksSent),
                    'personal_tasks'  =>DataInTask::collection($tasksRecived),
                ];
            }
        }

    }
}
