<?php

namespace App\Http\Controllers;

use App\Chapter;
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
use Carbon\Carbon;
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
                 'create_at' =>'date|nullable|date_format:d/m/Y',

             ]);
             if ($validator->fails()) {

         return response()->json(['errors'=>$validator->errors()]);
       }
            $committee = new Committee();
            $committee->name = strtolower($request->name);
            $chapter = Chapter::where('id',$request->chapter)->first();
            if ($chapter == null)
            {
                return response()->json(['error' => 'This Chapter Is Not Found']);
            }
            else{
                $committee->chapter_id =$request->chapter != null ? $request->chapter : null;

            }
            $committee->description =$request->description != null ? $request->description : null;
            $committee->created_at = $request->created_at != null ? $request->created_at : now();

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
    public function show($commId)
    {
        if (Committee::where('id', $commId)->first() != null) {
            $committee = Committee::findOrFail($commId);
            return new CommitteeData($committee);
        }else{
            return response()->json([
                'response' => 'Error',
                'message' =>  'Committee Not Found',
            ]);
        }
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

    public function update( $commId,Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' =>  'string|max:50 |min:2|required',
            'mentor' => 'nullable |numeric | min:0 | max:20000',
            'director' => 'nullable |numeric | min:1 | max:20000',
            'hr_coordinator' => 'nullable |numeric| min:1 | max:20000',
        ]);
        if ($validator->fails()) {

            return response()->json(['errors'=>$validator->errors()]);
        }
       $vol = Volunteer::where('user_id',auth()->user()->id)->first();
        $position = Position::where('id',$vol->position_id)->value('name');
            $seasonId = Season::where('isActive',1)->value('id');
        if ($position == 'chairperson' || ($position == 'vice-chairperson')) {
            if (Committee::where('id', $commId)->first() != null) {
                $committee = Committee::findOrFail($commId);

                $committee->name = Committee::where('name', strtolower($request->name))->first() != null ? $committee->name : strtolower($request->name);
                $committee->description = $request->description != null ? $request->description : $committee->description;
                $committee->update();
                $request->mentor != null ? self::updatePos('mentor', $request->mentor, $committee) : null;
                $request->director != null ? self::updatePos('director', $request->director, $committee) : null;
                $request->hr_coordinator != null ? self::updatePos('hr_coordinator', $request->hr_coordinator, $committee) : null;

//            if (Committee::where('name',strtolower($request->name))->first() != null)
//            {

                return response()->json(['success' => 'The Committee Has been updated successfully']);

//            }

            }  else{
                return response()->json([
                    'response' => 'Error',
                    'message' =>  'Committee Not Found',
                ]);
            }
        }
        else {

            return response()->json([
                'response' => 'Error',
                'message' =>  'Un Authenticated',
            ]);        }

    }
    public function updatePos($pos,$volId,$committee)
    {
        $seasonId = Season::where('isActive',1)->value('id');
        if (Volunteer::where('id',$volId)->first() == null) {
            return response()->json([
                'response' => 'Error',
                'message' =>  'Sorry, This Is A Participant Account',
            ]);
        }

        if($committee->volunteer()->wherePivot('position','=',$pos)->wherePivot('season_id',$seasonId)->first())
        {
            $committee->volunteer()->updateExistingPivot($volId , ['position'=>$pos,'season_id'=>$seasonId]);
        }
        else{
            $committee->volunteer()->attach($volId, ['position'=>$pos,'season_id'=>$seasonId,'committee_id'=>$committee->id]);
        }
    }

    // delete
    public function destroy($commId)
    {
       $vol = Volunteer::where('user_id',auth()->user()->id)->first();
       if($vol) {
           $seasonId = Season::where('isActive', 1)->value('id');
           $position = Position::where('id', $vol->position_id)->value('name');
           if ($position == 'chairperson' || ($position == 'vice-chairperson')) {
               if (Committee::where('id', $commId)->first() != null) {
                   $committee = Committee::findOrFail($commId);
                   $committee->volunteer;
                   $volunteers = $committee->volunteer()->wherePivot('committee_id', $committee->id)
                       ->wherePivot('season_id', $seasonId)->pluck('vol_id');
                   foreach ($volunteers as $key => $volunteer) {
                       $committee->volunteer()->detach($volunteer);
                   }

                   $committee->delete();
                   return response()->json([
                       'response' => 'Success',
                       'message' => 'The Committee Has Been Deleted Successfully',
                   ]);
               } else {
                   return response()->json([
                       'response' => 'Error',
                       'message' => 'The Committee Is Not Found',
                   ]);
               }
           }
           else {
               return response()->json([
                   'response' => 'Error',
                   'message' =>  'You are not allowed to create a chapter',
               ]);
           }
       }
       else {
            return response()->json([
                'response' => 'Error',
                'message' =>  'Un Authenticated',
            ]);
        }
    }
}
