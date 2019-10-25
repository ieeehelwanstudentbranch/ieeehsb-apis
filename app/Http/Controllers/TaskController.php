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
use App\Notification;
use App\SendTask;
use App\Task;
use App\Events\TaskEvent;
use App\User;
use function GuzzleHttp\Psr7\try_fopen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.auth');
    }

    public function createPage(){
        if ( JWTAuth::parseToken()->authenticate()->position == 'EX_com' || JWTAuth::parseToken()->authenticate()->position == 'highBoard') {
            $users = User::select('id', 'firstName', 'lastName', 'position')->get();
            return new CreateTaskPage($users);
        }else{
            return response()->json(['error'=>'Un Authenticated']);
        }
    }

    public function store(Request $request){
        if ( JWTAuth::parseToken()->authenticate()->position == 'EX_com' || JWTAuth::parseToken()->authenticate()->position == 'highBoard') {
            $this->validate($request, [
                'title' => 'required |min:3 |max:100 ',
                'body' => 'required |min:3',
                'deadline' => 'required',
                'files.*' => 'nullable|mimes:docx,doc,txt,csv,xls,xlsx,ppt,pptx,pdf,jpeg,jpg,png,svg,gif,ps,xd,ai,zip|max:524288',
                [
                    'files.*.mimes' => 'Only docx, doc, txt, csv, xls, xlsx, ppt, pptx, pdf, jpeg, jpg, png, svg, gif, ps, xd, ai, zip files are allowed',
                    'files.*.max' => 'Sorry! Maximum allowed size for an one file is 500MB',
                ],
                'to' => 'required',
            ]);

            foreach ($request->input('to') as $to) {
                $task = new Task();
                $task->title = $request->input('title');
                $task->body_sent = $request->input('body');
                $task->deadline = $request->input('deadline');
                $task->from = JWTAuth::parseToken()->authenticate()->id;
                $task->to = $to;
                $task->committee_id = User::findOrFail($to)->committee_id;
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
                event(new TaskEvent($task , 'send'));
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
    public function viewTask($id){
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
            $this->validate($request, [
                'body' => 'required |min:1',
                'files.*' => 'sometimes|file|mimes:docx,doc,txt,csv,xls,xlsx,ppt,pptx,pdf,jpeg,jpg,png,svg,gif,ps,xd,ai,zip|max:524288',
                [
                    'files.*.mimes' => 'Only docx,doc,txt,csv,xls,xlsx,ppt,pptx,pdf,jpeg,jpg,png,svg,gif,ps,xd,ai,zip files are allowed',
                    'files.*.max' => 'Sorry! Maximum allowed size for an one file is 500MB',
                ],
            ]);
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
                $task->files_deliver = json_encode($data);
            }
            $task->body_deliver = $request->input('body');
            $task->status = 'deliver';
            $task->update();
            event(new TaskEvent($task , 'deliver'));
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
        $task = Task::findOrFail($id);

        if ($task->from == JWTAuth::parseToken()->authenticate()->id){
            $this->validate($request, [
                'rate' => 'required|numeric|min:1|max:100',
                'evaluation' => 'required|min:3',
            ]);
            $task->status = 'accepted';
            $task->rate = $request->input('rate');
            $task->evaluation = $request->input('evaluation');
            $task->update();
            event(new TaskEvent($task , 'accept-task'));
            return response()->json([
                'response' => 'Success',
                'message' => 'Evaluating tasks done successfullty',
            ]);
        }else{
            return response()->json([
                'response' => 'Error',
                'message' => 'Sorry, You are Not Authorized to Evaluate this task.',
            ]);
        }
    }

    public function refuseTask($id){
        $task = Task::findOrFail($id);
        if ($task->from == JWTAuth::parseToken()->authenticate()->id){
            $task->status = 'pending';
            $task->update();
            event(new TaskEvent($task , 'refuse-task'));
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
}