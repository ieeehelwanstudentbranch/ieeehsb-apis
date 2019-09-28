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
use Illuminate\Validation\Rule;

class CommitteeController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.auth');
    }

    // index
    public function index()
    {
        $committees = Committee::orderBy('id', 'DESC')->paginate(100);
        return CommitteeCollection::collection($committees);
    }

    // view committee
    public function view($id)
    {
        $committees = Committee::findOrFail($id);
        return new CommitteeData($committees);
    }

    // add committee
    public function addPage()
    {
        if (auth()->user()->ex_com_option) {
            if (auth()->user()->position == 'EX_com' && (auth()->user()->ex_com_option->ex_options == 'chairperson' || auth()->user()->ex_com_option->ex_options == 'vice-chairperson')) {
                $committee = Committee::where('name', 'HR_OD')->get();
                if (count($committee) >= 1) {
                    return CommitteeResource::collection($committee);
                } else {
                    return new CommitteeResource(true);
                }
            } else {
                return response()->json(['error' => 'Un Authenticated']);
            }
        } else {
            return response()->json(['error' => 'Un Authenticated']);
        }

    }

    public function add(Request $request)
    {
        if (auth()->user()->position == 'EX_com' && (auth()->user()->ex_com_option->ex_options == 'chairperson' || auth()->user()->ex_com_option->ex_options == 'vice-chairperson')) {
            $this->validate($request, [
                'name' => 'required |string | unique:committees| max:50 | min:2',
                'mentor' => 'nullable |numeric | min:0 | max:20000',
                'director' => 'nullable |numeric | min:1 | max:20000',
                'hr_coordinator' => 'nullable |numeric| min:1 | max:20000',
            ]);

            $committee = new Committee();
            $committee->name = strtoupper($request->input('name'));

            if ($request->input('mentor')) {
                $mentor = User::findOrFail($request->input('mentor'));
                $committee->mentor = $mentor->firstName . ' ' . $mentor->lastName;
                $committee->mentor_id = $mentor->id;
            }

            if ($request->input('director')) {
                $director = User::findOrFail($request->input('director'));
                $committee->director = $director->firstName . ' ' . $director->lastName;
                $committee->director_id = $director->id;
            }

            if ($request->input('hr_coordinator')) {
                $hr_coordinator = User::findOrFail($request->input('hr_coordinator'));
                $committee->hr_coordinator = $hr_coordinator->firstName . ' ' . $hr_coordinator->lastName;
                $committee->hr_coordinator_id = $hr_coordinator->id;
            }
            $committee->save();
            return response()->json(['success' => 'Committee Added Successfully']);
        } else {
            return response()->json(['error' => 'Un Authenticated']);
        }

    }

    //Edit Committee
    public function updatePage()
    {
        if (auth()->user()->position == 'EX_com' && (auth()->user()->ex_com_option->ex_options == 'chairperson' || auth()->user()->ex_com_option->ex_options == 'vice-chairperson')) {
            $committee = Committee::where('name', 'HR_OD')->get();
            return CommitteeResource::collection($committee);
        } else {
            return response()->json(['error' => 'Un Authenticated']);
        }
    }

    public function update(Request $request, $id)
    {
        if (auth()->user()->position == 'EX_com' && (auth()->user()->ex_com_option->ex_options == 'chairperson' || auth()->user()->ex_com_option->ex_options == 'vice-chairperson')) {
            $this->validate($request, [
                'name' => ['required', Rule::unique('committees')->ignore($id)],
                'mentor' => 'required |string | max:100 | min:1',
                'director' => 'nullable |string | max:100 | min:1',
                'hr_coordinator' => 'nullable |string | max:100 | min:1',
            ]);
            $committee = Committee::findOrFail($id);
            $committee->name = $request->input('name');
            $mentor = User::findOrFail($request->input('mentor'));
            $committee->mentor = $mentor->firstName . ' ' . $mentor->lastName;
            $committee->mentor_id = $mentor->id;

            if ($request->input('director')) {
                $director = User::findOrFail($request->input('director'));
                $committee->director = $director->firstName . ' ' . $director->lastName;
                $committee->director_id = $director->id;
            }

            if ($request->input('hr_coordinator')) {
                $hr_coordinator = User::findOrFail($request->input('hr_coordinator'));
                $committee->hr_coordinator = $hr_coordinator->firstName . ' ' . $hr_coordinator->lastName;
                $committee->hr_coordinator_id = $hr_coordinator->id;
            }
            $committee->update();
            return response()->json(['success' => 'Committee Updated']);
        } else {
            return response()->json(['error' => 'Un Authenticated']);
        }

    }

    // delete
    public function destroy($id)
    {
        if (auth()->user()->position == 'EX_com' && (auth()->user()->ex_com_option->ex_options == 'chairperson' || auth()->user()->ex_com_option->ex_options == 'vice-chairperson')) {
            $committee = Committee::findOrFail($id);
            $user = User::where('committee_id', $id);
            if ($user) {
                $user_id = User::where('committee_id', $id)->pluck('id');
                if (count($user_id) > 0) {
                    $ex_option = Ex_com_options::where('user_id', $user_id);
                    $hb_option = HighBoardOptions::where('user_id', $user_id);
                    if ($ex_option) {
                        $ex_option->delete();
                    }
                    if ($hb_option) {
                        $hb_option->delete();
                    }
                }
            }
            $user->delete();
            $committee->delete();
            $committees = Committee::all();
            return CommitteeCollection::collection($committees);
        } else {
            return response()->json(['error' => 'Un Authenticated']);
        }
    }
}