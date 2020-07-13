<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Auth;
use App\Chapter;
use App\Role;
use App\Position;
class ChapterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     public function __construct()
    {
        $this->middleware('jwt.auth');
        $this->middleware('type:volunteer');

    }
    public function index()
    {
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Input::merge(array_map('trim', Input::all()));
        $validator = Validator::make($request->all(), [
            'name' => 'required |string | max:50 | min:3|unique:chapters',
        ]);
         if ($validator->fails()) {

         return response()->json(['errors'=>$validator->errors()]);
     }
        $vol = Volunteer::where('user_id',auth()->user()->id)->first();
        $position = Postion::where('id',$vol->position_id)->value('name');
        if ($position == 'chairperson' || ($position == 'vice-chairperson')) {
         $chapter = new Chapter;
         $chapter->name = strtolower($request->name);
         $chapter->save();
         $chairmanPos = new Position;
         $chairmanPos->name = 'chairperson ' . strtolower($request->name);
         $chairmanPos->role_id = Role::where('name','ex_com')->value('id');
         $chairmanPos->save();
         return response()->json(['success' =>'A New Chapter Has been added successfully']);
     }
    }
   
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
