<?php

namespace App\Http\Controllers;

use App\Committee;
use App\Volunteer;
use App\User;
use App\Position;
use App\Season;
use App\Role;
use App\Http\Resources\Committee\CommitteeCollection;
use App\Http\Resources\Committee\CommitteeData;
use App\Http\Resources\Committee\CommitteeResource;
use App\Http\Resources\User\UserData;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;



class CommitteeController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.auth');
        $this->middleware('type:volunteer');

    }

    // index
    public function index()
    {
        $committees = Committee::orderBy('id', 'DESC')->paginate(100);
        return CommitteeCollection::collection($committees);
    }

    // add committee
    public function create()
    {
         $vol = Volunteer::where('user_id',auth()->user()->id)->first();
        $position = Position::where('id',$vol->position_id)->value('name');
        if ($position == 'chairperson' || ($position == 'vice-chairperson')) {
                $committee = Committee::where('name', 'hr_od')->get();
                if (count($committee) >= 1) {
                    return CommitteeResource::collection($committee);
                } else {
                    return new CommitteeResource(true);
                }
            } else {
                return response()->json(['error' => 'Un Authenticated']);
            }

    }


    public function store(Request $request)
    {
        $vol = Volunteer::where('user_id',auth()->user()->id)->first();
        $position = Position::where('id',$vol->position_id)->value('name');
        if ($position == 'chairperson' || ($position == 'vice-chairperson')) {
             $validator = Validator::make($request->all(), [
                'name' => 'required |string | unique:committees| max:50 | min:2',
                'description' =>'nullable |string | max:4000 | min:2',
                'chapter' => 'nullable |numeric | min:1 | max:20000',
                'mentor' => 'nullable |numeric | min:0 | max:20000',
                'director' => 'nullable |numeric | min:1 | max:20000',
                'hr_coordinator' => 'nullable |numeric| min:1 | max:20000',
            ]);
             if ($validator->fails()) {

         return response()->json(['errors'=>$validator->errors()]);
       }

            $committee = new Committee();
            $committee->name = strtolower($request->name);
            $committee->chapter_id =$request->chapter != null ? $request->chapter : null;
            $committee->description =$request->description != null ? $request->description : null;
            $committee->save();
            $commId = $committee->id;

            $seasonId = Season::where('isActive',1)->value('id');
            if ($request->mentor) {
                $mentor = DB::table('vol_committees')->insert(
            [
                'vol_id' => $request->mentor,
                'committee_id' => $commId,
                'position' => 'mentor',
                'season_id' => $seasonId,
            ]);
            }

            elseif ($request->director) {
                  $director = DB::table('vol_committees')->insert(
            [
                'vol_id' => $request->director,
                'committee_id' => $commId,
                'position' => 'director',
                'season_id' => $seasonId,
            ]);
            }

            elseif ($request->hr_coordinator) {
                 $director = DB::table('vol_committees')->insert(
            [
                'vol_id' => $request->hr_coordinator,
                'committee_id' => $commId,
                'position' => 'hr_coordinator',
                'season_id' => $seasonId,
            ]);
            }
            return response()->json(['success' => 'Committee Added Successfully']);
        } else {
            return response()->json(['error' => 'Un Authenticated']);
        }

    }
     // view committee
    public function show(Committee $committee)
    {
        return new CommitteeData($committee);
    }


    //Edit Committee
    public function edit(Committee $committee)
    {
        $vol = Volunteer::where('user_id',auth()->user()->id)->first();
        $position = Position::where('id',$vol->position_id)->value('name');
        if ($position == 'chairperson' || ($position == 'vice-chairperson')) {
            $committee = Committee::where('name', 'hr-od')->get();
            return CommitteeResource::collection($committee);
        } else {
            return response()->json(['error' => 'Un Authenticated']);
        }
    }

    public function update(Committee $committee,Request $request)
    {
       $vol = Volunteer::where('user_id',auth()->user()->id)->first();
        $position = Position::where('id',$vol->position_id)->value('name');
            $seasonId = Season::where('isActive',1)->value('id');
        if ($position == 'chairperson' || ($position == 'vice-chairperson')) {
$validator = Validator::make($request->all(), [
                'name' => 'required', 'unique:committees',
                'mentor' => 'nullable |numeric | min:0 | max:20000',
                'director' => 'nullable |numeric | min:1 | max:20000',
                'hr_coordinator' => 'nullable |numeric| min:1 | max:20000',
            ]);
             if ($validator->fails()) {

             return response()->json(['errors'=>$validator->errors()]);
         }
            $committee->name = $request->name;
            $committee->description = $request->description != null ? $request->description:$committee->description;
            $committee->update();

           if ($request->mentor) {
                self::updatePos('mentor',$request->mentor,$committee);
            }
            elseif ($request->director) {
                self::updatePos('director',$request->director,$committee);

            }
            elseif ($request->hr_coordinator) {
                self::updatePos('hr_coordinator',$request->hr_coordinator,$committee);

            }
            return response()->json(['success' => 'Committee Updated']);
        } else {
            return response()->json(['error' => 'Un Authenticated']);
        }

    }
    public function updatePos($pos,$volId,$committee)
    {
        $seasonId = Season::where('isActive',1)->value('id');
        if($committee->volunteer()->wherePivot('position','=',$pos)->wherePivot('season_id',$seasonId)->first())
        {
            $committee->volunteer()->updateExistingPivot($volId , ['position'=>$pos,'season_id'=>$seasonId]);
        }
        else{
            $committee->volunteer()->attach($volId, ['position'=>$pos,'season_id'=>$seasonId,'committee_id'=>$committee->id]);
        }
    }

    // delete
    public function destroy(Committee $committee)
    {
       $vol = Volunteer::where('user_id',auth()->user()->id)->first();
        $seasonId = Season::where('isActive',1)->value('id');
        $position = Position::where('id',$vol->position_id)->value('name');
        if ($position == 'chairperson' || ($position == 'vice-chairperson')) {
            $committee->volunteer;
            $volunteers = $committee->volunteer()->wherePivot('committee_id',$committee->id)
            ->wherePivot('season_id',$seasonId)->pluck('vol_id');
            foreach ($volunteers as $key => $volunteer) {
                $committee->volunteer()->detach($volunteer);
            }

            $committee->delete();
            $committees = Committee::all();
            return CommitteeCollection::collection($committees);
        } else {
            return response()->json(['error' => 'Un Authenticated']);
        }
    }
}
