<?php

namespace App\Http\Controllers;

use Auth;
use App\Role;
use App\Chapter;
use App\Position;
use App\Committee;
use App\Volunteer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\Chapter\ChapterCollection;
use App\Http\Resources\Chapter\ChapterResource;


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
        $chapters = Chapter::orderBy('id', 'DESC')->paginate(5);
        return ChapterResource::collection( $chapters);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $chapters = Chapter::orderBy('id', 'DESC')->paginate(5);
        return new ChapterCollection($chapters);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    /**
     * @SWG\Post(
     *   path="/api/chapter/",
     *   summary="Add new chapter",
     *   operationId="chapterId",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=406, description="not acceptable"),
     *   @SWG\Response(response=500, description="internal server error"),
     *@SWG\Parameter(
     *          name="firstName",
     *          in="query",
     *      description="testing data",
     *          required=true,
     *          type="string",
     *     ),
     *@SWG\Parameter(
     *          name="lastName",
     *          in="query",
     *      description="testing data",
     *          required=true,
     *          type="string",
     *     ),
     *@SWG\Parameter(
     *          name="facutly",
     *          in="query",
     *      description="testing data",
     *          required=true,
     *          type="string",
     *     ),
     *@SWG\Parameter(
     *          name="university",
     *          in="query",
     *      description="testing data",
     *          required=true,
     *          type="string",
     *     ),
     *@SWG\Parameter(
     *          name="DOB",
     *          in="query",
     *      description="testing data",
     *          required=true,
     *          type="string",
     *     ),



     *   )


     *
     */
    public function store(Request $request)
    {
        //description


        $vol = Volunteer::where('user_id', auth()->user()->id)->first();
        if ($vol) {
            $position = Position::where('id', $vol->position_id)->value('name');
            if ($position == 'chairperson' || ($position == 'vice-chairperson')) {
                $validator = Validator::make($request->all(), [
                    'name' => 'required |string | max:50 | min:3|unique:chapters',
                    'description' => 'nullable|string|min:2',
                    'chairperson' => 'nullable|numeric|min:1',
                    'logo' => 'image|nullable|max:500000 |mimes:jpg,png,jpeg,svg,gif,tiff,tif',
                    'create_at' => 'date|nulable|date_format:d/m/Y',
                ]);
                if ($validator->fails()) {

                    return response()->json(['errors' => $validator->errors()]);
                }
                $chapter = new Chapter;
                $chapter->name = strtolower($request->name);
                $chapter->created_at = $request->created_at != null ? $request->created_at : Carbon::now();
                $chapter->description = $request->description != null ? $request->description : null;
                $chapter->created_at = $request->created_at != null ? $request->created_at : Carbon::now();

                if ($request->file('logo')) {
                    $filenameWithExtention = $request->file('logo')->getClientOriginalName();
                    $fileName = pathinfo($filenameWithExtention, PATHINFO_FILENAME);
                    $extension = $request->file('logo')->getClientOriginalExtension();
                    $fileNameStoreImage = $fileName . '_' . time() . '.' . $extension;
                    $path = $request->file('logo')->move('public/logo/', $fileNameStoreImage);
                    $chapter->logo = $path;
                }
                if ($request->chairperson != null) {
                    if (Volunteer::where('id', $request->chairperson)->first() == null) {
                        return response()->json(['errors' => 'Sorry, This is a participant account']);
                    } else {
                        $chair = Volunteer::findOrFail($request->chairperson);
                        if (strpos($chair->position->name, $chapter->name)) {
                            $chapter->chairperson_id = $request->chairperson;
                        } else {
                            return response()->json([
                                'response' => 'Error',
                                'message' => 'You are the ' . $chair->position->name . ' of the Branch,You can not be the chairman of this chpater',
                            ]);
                        }
                    }
                }
                $chapter->save();


                Position::updateOrCreate([
                    'name' => 'chairperson ' . strtolower($request->name),
                    'role_id' => Role::where('name', 'ex_com')->value('id')
                ]);
                return response()->json([
                    'response' => 'Success',
                    'message' => 'A New Chapter Has been added successfully',
                ]);
            } else {
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Chapter $chapter)
    {
        $chapter;
        return new ChapterResource($chapter);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Chapter $chapter)
    {
        $chapter;
        return new ChapterResource($chapter);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Chapter $chapter)
    {
         $validator = Validator::make($request->all(), [
            'name' => 'string|max:50 |min:3|required',
            'description' => 'nullable |string| min:2',
            'chairperson' => 'nullable|numeric|min:1',
            'logo' => 'image|nullable|max:500000 |mimes:jpg,png,jpeg,svg,gif,tiff,tif',

        ]);
         if ($validator->fails()) {

         return response()->json(['errors'=>$validator->errors()]);
     }

        $vol = Volunteer::where('user_id',auth()->user()->id)->first();
        if ($vol) {
        $position = Position::where('id',$vol->position_id)->value('name');
        if ($position == 'chairperson' || ($position == 'vice-chairperson')) {

            $chapter->name = Chapter::where('name', strtolower($request->name))->first() != null ? $chapter->name : strtolower($request->name);
            $chapter->description = $request->description != null ? $request->description : $chapter->description;
            $chapter->updated_at = Carbon::now();

            if ($request->file('logo')) {
                $filenameWithExtention = $request->file('logo')->getClientOriginalName();
                $fileName = pathinfo($filenameWithExtention, PATHINFO_FILENAME);
                $extension = $request->file('logo')->getClientOriginalExtension();
                $fileNameStoreImage = $fileName . '_' . time() . '.' . $extension;
                $path = $request->file('logo')->move('public/logo/', $fileNameStoreImage);
                $chapter->logo = $path;
            }
            if ($request->chairperson != null) {
                if (Volunteer::where('id', $request->chairperson)->first() == null) {
                    return response()->json([
                        'response' => 'Error',
                        'message' => 'Sorry, This is a participant account',
                    ]);

                } else {
                    $chair = Volunteer::findOrFail($request->chairperson);
                    if (strpos($chair->position->name, $chapter->name)) {
                        $chapter->chairperson_id = $request->chairperson;
                    } else {
                        return response()->json([
                            'response' => 'Error',
                            'message' => 'You are the ' . $chair->position->name .
                                ' of the Branch,You can not be the chairman of this chpater',
                        ]);

                    }
                }
            }
            $chapter->update();
            if (Chapter::where('name', strtolower($request->name))->first() != null) {
                return response()->json(['success' => 'The Chapter Has been updated successfully', 'error' => 'Except the name  because it is stored before']);

            }
            return response()->json([
                'response' => 'Success',
                'message' => 'The Chapter Has been updated successfully',
            ]);
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Chapter $chapter)
    {
        $vol = Volunteer::where('user_id',auth()->user()->id)->first();
        if ($vol)
        {
        $position = Position::where('id',$vol->position_id)->value('name');
        if ($position == 'chairperson' || ($position == 'vice-chairperson')) {
            $committees = Committee::where('chapter_id', $chapter->id)->get();
            foreach ($committees as $key => $comm) {
                $comm->chapter_id = 0;
                $comm->update();
            }

            $chapter->delete();
            return response()->json([
                'response' => 'Success',
                'message' => 'The Chapter Has been deleted successfully',
            ]);
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
