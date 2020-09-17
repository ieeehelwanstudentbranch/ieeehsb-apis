<?php

namespace App\Http\Resources\Task;

use App\Committee;
use App\DeliverTask;
use App\Status;
use App\Task;
use App\User;
use App\Volunteer;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\Types\Parent_;
use Tymon\JWTAuth\Facades\JWTAuth;

class CompleteTasks extends Resource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */

    public function info($pos)
    {
        $comm = DB::table('committees')
            ->join('vol_committees','committees.id','=','vol_committees.committee_id')
            ->join('volunteers','volunteers.id','vol_committees.vol_id')
                ->where('vol_committees.position', '=', $pos )
                ->where('season_id',DB::table('seasons')->where('isActive',1)->value('id'))
                ->where('vol_committees.vol_id','volunteers.id')
            ->select('committees.id','committees.name')->get();
        return $comm;
    }
    public function toArray($request)
    {
        $status = Status::where('name','accepted')->value('id');
//        $user = User::where('id',JWTAuth::parseToken()->authenticate()->id);
//        $vol = Volunteer::where('user_id',JWTAuth::parseToken()->authenticate()->id)->first();

        $committees_mentor = self::info('mentor');
        $committees_hr_od = self::info('hr_coordinator');;
        $tasksSent = Task::where('from', JWTAuth::parseToken()->authenticate()->id)
            ->where('status_id',$status)->get();
        $tasksRecived = Task::where('to', JWTAuth::parseToken()->authenticate()->id)->where('status_id',$status)->get();
        try {
            foreach ($committees_mentor as $committee) {
                $committeeTasks[] = DataInTask::collection(Task::query()->where('committee_id', $this->comm_id)->where('status_id',$status)->get());
            }
            return [
                'mentoring_tasks' => $committeeTasks,
                'sent_tasks' => DataInTask::collection($tasksSent),
                'personal_tasks' => DataInTask::collection($tasksRecived),
            ];
        } catch (\Exception $e) {
            try {
                foreach ($committees_hr_od as $committee) {
                    $committeeTask[] = DataInTask::collection(Task::query()->where('committee_id', $committee->id)->where('status_id',$status)->get());
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
