<?php

namespace App\Http\Controllers;

use App\Committee;
use App\Ex_com_options;
use App\HighBoardOptions;
use App\Http\Resources\Committee\CommitteeCollection;
use App\Http\Resources\Committee\CommitteeData;
use App\Http\Resources\Committee\CommitteeResource;
use App\Http\Resources\User\UserData;
use App\User;
use Illuminate\Http\Request;

class CommitteeController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.auth');
    }

//    index
    public function index()
    {
        $committees = Committee::orderBy('id', 'DESC')->paginate(100);
        return CommitteeCollection::collection($committees);
    }

//    view committee
    public function view($id)
    {
        $committees = Committee::findOrFail($id);
        return new CommitteeData($committees);
    }

//    add committee
    public function addPage()
    {
        if(auth()->user()->position == 'EX_com' && (auth()->user()->ex_com_option->ex_options=='chairperson' || auth()->user()->ex_com_option->ex_options =='vice-chairperson')){

            $committee = Committee::where('name','hr_od')->get();
            return CommitteeResource::collection($committee);
        } else {
            return response()->json('error');
        }

    }

    public function add(Request $request)
    {
        if(auth()->user()->position == 'EX_com' && (auth()->user()->ex_com_option->ex_options=='chairperson' || auth()->user()->ex_com_option->ex_options =='vice-chairperson')){
            $this->validate($request ,[
                'name' => 'required |string | unique:committees| max:50 | min:2',
                'mentor' => 'required |string | max:50 | min:2',
                'director' => 'nullable |string | max:50 | min:1',
                'hr_coordinator' => 'nullable |string | max:50 | min:1',
            ]);

            $committee = new Committee();
            $user =User::all();
            $committee->name = $request->input('name');

            $committee->Ex_com_Mentor = $request->input('mentor');

            $director=User::findOrFail($request->input('director'));
            $committee->director =$director->firstName .' '.$director->lastName;
            $committee->director_id = $director->id;

            $hr_coordinator =User::findOrFail($request->input('hr_coordinator'));
            $committee->hr_coordinator = $hr_coordinator->firstName .' '.$hr_coordinator->lastName;
            $committee->hr_coordinator_id = $hr_coordinator->id;

            $committee->save();

            return redirect()->action('CommitteeController@index')->with('Committee Added');
        }

    }

    //Edit Committee
    public function updatePage()
    {
        if(auth()->user()->position == 'EX_com' && (auth()->user()->ex_com_option->ex_options=='chairperson' || auth()->user()->ex_com_option->ex_options =='vice-chairperson')){

            $committee = Committee::where('name','hr_od')->get();
            return CommitteeResource::collection($committee);
        }

    }

    public function update(Request $request , $id)
    {
        if(auth()->user()->position == 'EX_com' && (auth()->user()->ex_com_option->ex_options=='chairperson' || auth()->user()->ex_com_option->ex_options =='vice-chairperson')){
            $this->validate($request ,[
                'name' => 'required |string | unique:committees| max:50 | min:2',
                'mentor' => 'required |string | max:50 | min:2',
                'director' => 'nullable |string | max:50 | min:2',
                'hr_coordinator' => 'nullable |string | max:50 | min:2',
            ]);

            $committee = Committee::findOrFail($id);
            $committee->name = $request->input('name');
            $committee->Ex_com_Mentor = $request->input('mentor');
            $committee->director = $request->input('director');
            $committee->hr_coordinator = $request->input('hr_coordinator');
            $committee->update();

            return redirect()->action('CommitteeController@index')->with('Committee Updated');
        }

    }

//    delete
    public function destroy($id)
    {
        if (auth()->user()->position == 'EX_com' && (auth()->user()->ex_com_option->ex_options == 'chairperson' || auth()->user()->ex_com_option->ex_options == 'vice-chairperson')) {

            $committee = Committee::findOrFail($id);
            $user = User::where('committee_id', $id);
            if ($user){
                $user_id = User::where('committee_id', $id)->pluck('id');
                if (count($user_id) > 0) {
                    $ex_option = Ex_com_options::where('user_id', $user_id);
                    $hb_option = HighBoardOptions::where('user_id', $user_id);
                    if ($ex_option){$ex_option->delete();}
                    if ($hb_option){$hb_option->delete();}
                }
            }
            $user->delete();
            $committee->delete();

            $committees = Committee::all();
            return CommitteeCollection::collection($committees);
        }
    }

}
