<?php

namespace App\Http\Controllers;

use App\Award;
use App\Chapter;
use App\Volunteer;
use App\Position;
use Validator;
use Illuminate\Http\Request;
use App\Http\Resources\Award\AwardResource;
use Tymon\JWTAuth\Facades\JWTAuth;

class AwardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
         $this->middleware('jwt.auth');
        // $this->middleware('type:volunteer');

    }
    public function index()
    {
        $awards = Award::orderBy('id', 'DESC')->paginate(5);
        return AwardResource::collection($awards);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
            $vol = Volunteer::where('user_id',auth()->user()->id)->first();
        if($vol != null) {
            if ($vol->position->name == 'chairperson' || ($vol->position->name == 'vice-chairperson' || ($vol->position->name == 'secratory'))) {
                $validator = Validator::make($request->all(), [
                    'name' => 'required |string | max:50 | min:3|unique:awards',
                    'information' => 'required|string|min:2',
                    'location' => 'nullable|string|min:2',
                    'image' => 'image|nullable|max:500000 |mimes:jpg,png,jpeg,svg,gif,tiff,tif',
                    'date' => 'required|date|date_format:Y-m-d',
                    'chapterId' => 'nullable|numeric',
                    //meen 25d el award el branch wla el chapter
                ]);
                if ($validator->fails()) {
                    return response()->json(['errors' => $validator->errors()]);
                }
                $award = new Award;
                $award->name = $request->name;
                $award->information = $request->information != null ? $request->information : $award->information;
                $award->location = $request->location != null ? $request->location : null;
                $award->date = $request->date;
                if ($request->file('image')) {
                    $filename = $request->file('image')->store('public/awards/');
                    $award->image = trim($filename, 'public');
                }
                $award->to = $request->chapterId != null ? $request->chapterId : null;
                $award->save();

            } else {
                return response()->json([
                    'response' => 'Error',
                    'message' => 'Sorry, You are Not Authorized to Create The Award.',
                ]);
            }
        }
        else{
            return response()->json([
                'response' => 'Error',
                'message' => 'Sorry, You are Not Authorized to Create The Award.',
            ]);
        }


    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Award  $award
     * @return \Illuminate\Http\Response
     */
    public function show(Award $award)
    {
        return new AwardResource($award);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Award  $award
     * @return \Illuminate\Http\Response
     */
    public function edit(Award $award)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Award  $award
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Award $award)
    {
            $vol = Volunteer::where('user_id',JWTAuth::parseToken()->authenticate()->id)->first();
        if($vol != null)
        {
            if ($vol->position->name == 'chairperson' || ($vol->position->name == 'vice-chairperson' ||($vol->position->name == 'secratory') ) )
            {
                $validator = Validator::make($request->all(), [
            'name' => ' string | max:50 | min:3|required|unique:awards',
            'information' =>'nullable|string|min:2',
            'location' =>'nullable|string|min:2',
            'image' => 'image|nullable|max:500000 |mimes:jpg,png,jpeg,svg,gif,tiff,tif',
            'date' => 'nullable|date|date_format:Y-m-d',
            'chapterId' =>'nullable|numeric',
            //meen 25d el award el branch wla el chapter
                ]);
                if ($validator->fails())
                {
                    return response()->json(['errors'=>$validator->errors()]);
                }
                $award->name = $request->name;
                $award->information = $request->information!= null ? $request->information : $award->information;
                $award->location = $request->location!= null ? $request->location : $award->location;
                $award->date = $request->date!= null ? $request->date:$award->date;
                if ($request->file('image')) {
                    $filename = $request->file('image')->store('public/awards/');
                    $award->image = trim($filename, 'public');
                }
                $award->to = $request->chapterId!= null ? $request->chapterId : null;
                $award->update();

            }
    }
  }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Award  $award
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Award $award)
    {
         $vol = Volunteer::where('user_id',JWTAuth::parseToken()->authenticate()->id)->first();
        if($vol != null)
        {
            if ($vol->position->name == 'chairperson' || ($vol->position->name == 'vice-chairperson' ||($vol->position->name == 'secratory') ) )
            {
                $award->delete();
            }
    }
        else{
            return response()->json([
                'response' => 'Error',
                'message' => 'Sorry, You are Not Authorized to delete The Award.',
            ]);
        }
}
}
