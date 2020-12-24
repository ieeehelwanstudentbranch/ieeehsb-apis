<?php

namespace App\Http\Resources\Task;

use App\Chapter;
use App\Season;
use App\Status;
use Illuminate\Http\Resources\Json\JsonResource;
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

class TaskPage extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function info($pos,$volId)
    {
        $comm = DB::table('committees')
            ->join('vol_committees','committees.id','=','vol_committees.committee_id')
            ->join('volunteers','volunteers.id','vol_committees.vol_id')
            ->where('vol_committees.position', '=', $pos )
            ->where('season_id',DB::table('seasons')->where('isActive',1)->value('id'))
            ->where('vol_committees.vol_id',$volId)
            ->select('committees.id','committees.name')->get();

        return $comm;
    }
    public function status($s)
    {
        $stat = Status::where('name',$s)->value('id');
        return $stat;

    }
    public function toArray($request)
    {
        $vol = Volunteer::where('user_id',JWTAuth::parseToken()->authenticate()->id)->first();
        $committees_mentor = self::info('mentor',$vol->id)->toArray();
        $committees_hr_od = self::info('hr_coordinator',$vol->id)->toArray();
//        $tasksSent = Task::where('from', JWTAuth::parseToken()->authenticate()->id)->where('status','pending')->get();

        $tasksSent = Task::where('from', JWTAuth::parseToken()->authenticate()->id)->where(function ($q) {
            $q->where('status_id', self::status('pending'))
                ->orWhere('status_id', self::status('delivered'));
        })->get();

        $tasksRecived = Task::where('to', JWTAuth::parseToken()->authenticate()->id)->where(function ($q) {
            $q->where('status_id', self::status('pending'))
                ->orWhere('status_id', self::status('delivered'));
        })->get();

        if ($committees_mentor != null) {
            foreach ($committees_mentor as $committee) {
                if ($committee == null) {
                    return $this->response()->json(['response' => 'Error',
                        'message' => 'Committee not found']);
                } else {
                    $committeeTasks[] = DataInTask::collection(Task::query()->where('comm_id', $committee->id)->where(function ($q) {
                        $q->where('status_id', self::status('pending'))
                            ->orWhere('status_id', self::status('delivered'));
                    })->get());
                }
                return [
                    'mentoring_tasks' => $committeeTasks,
                    'sent_tasks' => DataInTask::collection($tasksSent),
                    'personal_tasks' => DataInTask::collection($tasksRecived),
                ];
            }
        }
        elseif ($committees_hr_od != null) {
            foreach ($committees_hr_od as $committee) {
                if ($committee == null) {
                    return $this->response()->json(['response' => 'Error',
                        'message' => 'Committee not found']);
                } else {
                    $committeeTask[] = DataInTask::collection(Task::query()->where('comm_id', $committee->id)->where(function ($q) {
                        $q->where('status_id', self::status('pending'))
                            ->orWhere('status_id', self::status('delivered'));
                    })->get());
                }
                return [
                    'coordinating_tasks' => $committeeTask,
                    'sent_tasks' => DataInTask::collection($tasksSent),
                    'personal_tasks' => DataInTask::collection($tasksRecived),
                ];
            }
        }else return [
                'sent_tasks' => DataInTask::collection($tasksSent),
                'personal_tasks' => DataInTask::collection($tasksRecived),
            ];
        }

}
