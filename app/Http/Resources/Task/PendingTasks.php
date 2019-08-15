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
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $committees_mentor = Committee::query()->where('mentor_id', JWTAuth::parseToken()->authenticate()->id)->get();
        $committees_hr_od = Committee::query()->where('hr_coordinator_id', JWTAuth::parseToken()->authenticate()->id)->get();
//        $tasksSent = Task::where('from', JWTAuth::parseToken()->authenticate()->id)->where('status','pending')->get();

        $tasksSent = Task::where('from', JWTAuth::parseToken()->authenticate()->id)->where(function ($q) {
            $q->where('status', 'pending')
                ->orWhere('status', 'deliver');
        })->get();

        $tasksRecived = Task::where('to', JWTAuth::parseToken()->authenticate()->id)->where(function ($q) {
            $q->where('status', 'pending')
                ->orWhere('status', 'deliver');
        })->get();

        try {
            foreach ($committees_mentor as $committee) {
                $committeeTasks[] = DataInTask::collection(Task::query()->where('committee_id', $committee->id)->where(function ($q) {
                    $q->where('status', 'pending')
                        ->orWhere('status', 'deliver');
                })->get());
            }
            return [
                'mentoring_tasks' => $committeeTasks,
                'sent_tasks' => DataInTask::collection($tasksSent),
                'personal_tasks' => DataInTask::collection($tasksRecived),
            ];
        } catch (\Exception $e) {
            try {
                foreach ($committees_hr_od as $committee) {
                    $committeeTask[] = DataInTask::collection(Task::query()->where('committee_id', $committee->id)->where(function ($q) {
                        $q->where('status', 'pending')
                            ->orWhere('status', 'deliver');
                    })->get());
                }
                return [
                    'coordinating_tasks' => $committeeTask,
                    'sent_tasks' => DataInTask::collection($tasksSent),
                    'personal_tasks' => DataInTask::collection($tasksRecived),
                ];
            } catch (\Exception $e) {
                return [
                    'sent_tasks' => DataInTask::collection($tasksSent),
                    'personal_tasks' => DataInTask::collection($tasksRecived),
                ];
            }
        }
    }
}