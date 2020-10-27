<?php

namespace App\Http\Controllers;

use App\Committee;
use App\DeliverTask;
use App\Http\Resources\Task\CompleteTasks;
use App\Http\Resources\Task\CreateTaskPage;
use App\Http\Resources\Task\MentorViewTasks;
use App\Http\Resources\Task\MintorViewTasks;
use App\Http\Resources\Task\PendingTasks;
use App\Http\Resources\Task\TaskCollection;
use App\Http\Resources\Task\TaskCollectionPanding;
use App\Http\Resources\Task\TaskPage;
use App\Notification;
use App\Position;
use App\Season;
use App\SendTask;
use App\Status;
use App\Task;
use App\Events\TaskEvent;
use App\User;
use App\Volunteer;
use Illuminate\Support\Facades\Validator;
use function GuzzleHttp\Psr7\try_fopen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.auth');
        $this->middleware('type:volunteer');

    }
    public function index()
    {
            $tasks = Task::all();
            return new TaskPage($tasks);
//        }
    }

    public function create(){
        $vol = Volunteer::where('user_id',JWTAuth::parseToken()->authenticate()->id)->first();
        $pos = $vol->position;
        if ($pos->role->name == 'ex_com' || ($pos->role->name == 'highboard')) {
            $volunteers = Volunteer::all();
            return new CreateTaskPage($volunteers);
        }else{
            return response()->json(['error'=>'Un Authenticated']);
        }
    }

    public function store(Request $request){
        $vol = Volunteer::where('user_id',JWTAuth::parseToken()->authenticate()->id)->first();
        $pos = $vol->position;
        if ($pos->role->name == 'ex_com' || ($pos->role->name == 'highboard')) {
            $validator = Validator::make($request->all(), [

                'title' => 'min:3 |max:100|required',
                'body' => 'min:3|required',
                'deadline' => 'date_format:Y-m-d H:i:s|after:today|required',
                'files.*' => 'nullable|mimes:docx,doc,txt,csv,xls,xlsx,ppt,pptx,pdf,jpeg,jpg,png,svg,gif,ps,xd,ai,zip|max:524288',
                [
                    'files.*.mimes' => 'Only docx, doc, txt, csv, xls, xlsx, ppt, pptx, pdf, jpeg, jpg, png, svg, gif, ps, xd, ai, zip files are allowed',
                    'files.*.max' => 'Sorry! Maximum allowed size for an one file is 500MB',
                ],
                'to' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors'=>$validator->errors()]);
            }
            foreach ($request->to as $to) {
                $task = new Task();
                $task->title = $request->title;
                $task->body_sent = $request->body;
                $task->deadline = $request->deadline;
                $task->from = JWTAuth::parseToken()->authenticate()->id;
                $task->to = $to;
                $vol = Volunteer::find($to);
                $task->comm_id = $vol->committee->first()->id;
                $task->status_id = Status::where('name','pending')->value('id');
                // upload files
                if ($request->hasfile('files')) {
                    foreach ($request->file('files') as $file) {
                        $filenameWithExtention = $file->getClientOriginalName();
                        $fileName = pathinfo($filenameWithExtention, PATHINFO_FILENAME);
                        $extension = $file->getClientOriginalExtension();
                        $fileNameStore = $fileName . '_' . time() . '.' . $extension;
                        $file->move(base_path() . '/storage/app/public/tasks_sent', $fileNameStore);
                        $data[] = $fileNameStore;
                    }
                    $task->files_sent = json_encode($data);
                }
                $task->save();
//                event(new TaskEvent($task , 'send'));
            }
            return response()->json(['success'=>'task sent successfully']);
        } else {
            return response()->json(['error'=>'Un Authenticated']);
        }
    }

    // pending tasks
    public function pendingTasks(){

        $tasks = Task::all();
        return new PendingTasks($tasks);
    }
    
    // complete tasks
    public function completeTasks(){
        $tasks = Task::all();
        return new CompleteTasks($tasks);
    }
    
    // view task
    public function show($id){
        if (Task::all()->where('to', JWTAuth::parseToken()->authenticate()->id) || Task::all()->where('from', JWTAuth::parseToken()->authenticate()->id)
        ||(Task::all()->where('to', JWTAuth::parseToken()->authenticate()->id))
        ){
        return new TaskCollectionPanding(Task::findOrFail($id));
        }else{
            return response()->json(['error'=>'Un Authenticated']);
        }
    }
    
    // deliver task
    public function deliverTask(Request $request ,$id){
        $task = Task::findOrFail($id);
        if ($task->to == JWTAuth::parseToken()->authenticate()->id){
            $validator = Validator::make($request->all(), [
                'body' => 'required |min:1',
                'files.*' => 'sometimes|file|mimes:docx,doc,txt,csv,xls,xlsx,ppt,pptx,pdf,jpeg,jpg,png,svg,gif,ps,xd,ai,zip|max:524288',
                [
                    'files.*.mimes' => 'Only docx,doc,txt,csv,xls,xlsx,ppt,pptx,pdf,jpeg,jpg,png,svg,gif,ps,xd,ai,zip files are allowed',
                    'files.*.max' => 'Sorry! Maximum allowed size for an one file is 500MB',
                ],
            ]);
            if ($validator->fails()) {
                return response()->json(['errors'=>$validator->errors()]);
            }
            // upload files
            if ($request->hasfile('files')) {
                foreach ($request->file('files') as $file) {
                    $filenameWithExtention = $file->getClientOriginalName();
                    $fileName = pathinfo($filenameWithExtention, PATHINFO_FILENAME);
                    $extension = $file->getClientOriginalExtension();
                    $fileNameStore = $fileName . '_' . time() . '.' . $extension;
                    $file->move(base_path() . '/storage/app/public/tasks_delivered', $fileNameStore);
                    $data[] = $fileNameStore;
                }
                $task->files_delivered = json_encode($data);
            }

            $task->body_delivered = $request->body;
            $task->status_id = Status::where('name','delivered')->value('id');
            $task->update();
//            event(new TaskEvent($task , 'deliver'));
            return response()->json([
                'response' => 'Success',
                'message' => 'Congratulations, You had delivered this task successfully, Keep Going.',
            ]);
        } else {
            return response()->json([
                'response' => 'Error',
                'message' => 'Sorry, You are Not Authorized to access or deliver this task.',
            ]);
        }
    }

    public function acceptTask(Request $request , $id){
        if ($task = Task::find($id) ) {

            if ($task->from == JWTAuth::parseToken()->authenticate()->id) {

                $validator = Validator::make($request->all(), [
                    'rate' => 'numeric|required|min:1|max:100',
                    'evaluation' => 'string|required|min:3',
                ]);
                if ($validator->fails()) {
                    return response()->json(['errors' => $validator->errors()]);
                }
                $task->status_id = Status::where('name', 'approved')->value('id');
                $task->rate = $request->rate;
                $task->evaluation = $request->evaluation;
                $task->update();
//            event(new TaskEvent($task , 'accept-task'));
                return response()->json([
                    'response' => 'Success',
                    'message' => 'Evaluating tasks done successfullty',
                ]);
            } else {
                return response()->json([
                    'response' => 'Error',
                    'message' => 'Sorry, You are Not Authorized to Evaluate this task.',
                ]);
            }
        }
        else{
            return response()->json([
                'response' => 'Error',
                'message' => 'Task not found',
            ]);
        }
    }


    public function refuseTask($id){
        $task = Task::findOrFail($id);
        if ($task->from == JWTAuth::parseToken()->authenticate()->id){
            $task->status_id = Status::where('name','pending')->value('id');
            $task->update();
//            event(new TaskEvent($task , 'refuse-task'));
            return response()->json([
                'response' => 'Success',
                'message' => 'The task has been refused successfully',
            ]);
        }else{
            return response()->json([
                'response' => 'Error',
                'message' => 'Sorry, You are Not Authorized to refuse this task.',
            ]);
        }
    }
    public function update(Request $request, $id)
    {
        $task = Task::findOrFail($id);
        if ($task = Task::find($id)) {
            $vol = Volunteer::where('user_id', JWTAuth::parseToken()->authenticate()->id)->first();
            $pos = $vol->position;

            if ($task->from == JWTAuth::parseToken()->authenticate()->id || $pos->name == 'chairperson') {
                $task->body = $request->body;
                $task->update();
            }
        } else {
            return response()->json([
                'response' => 'Error',
                'message' => 'Task not found',
            ]);
        }
    }
        public function makeFeedback($id, Request $request)
    {
        if ($task = Task::find($id)) {
            $vol = Volunteer::where('user_id', JWTAuth::parseToken()->authenticate()->id)->first();
            $pos = $vol->position;
            $validator = Validator::make($request->all(), [
                'feedback' => 'numeric|required|min:1|max:100',
                'evaluation' => 'string|required|min:3',
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()]);
            }
            if ($pos->name == 'volunteer')
            {
                $committee = $vol->committee->id;
                $hr_od = $committee->volunteer()->where('position','hr_coordinator')->where('season_id',Season::where('is_Active',1)->value())->get();
            }
            if ($pos->name == 'chairperson' || $hr_od != null)
                {
                    $task->feedback = $request->feedback;


                }
            
            }
    }

}