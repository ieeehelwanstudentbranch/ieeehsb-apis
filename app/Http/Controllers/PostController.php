<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Committee;
use App\Volunteer;
use App\Status;
use App\Events\PostEvent;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\Post\PostCollection;
use App\Http\Resources\Post\PostResource;
use App\Post;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Facades\JWTAuth;

class PostController extends Controller
{

    public function __construct()
    {
        $this->middleware('jwt.auth');
        $this->middleware('type:volunteer');


    }

    public function index($committeeId)
    {
        $committee = Committee::findOrFail($committeeId);
        $volComm = $committee->volunteer->pluck('id')->toArray();
         $vol = Volunteer::where('user_id',auth()->user()->id)->first()->value('id');
         if(in_array($vol, $volComm))
         {
            $posts = $committee->post()->orderBy('created_at', 'desc')->paginate(50);
            return PostCollection::collection($posts);
         }


         else{
                return response()->json('you are not in comm post page');

         }


        // $posts = Post::orderBy('created_at', 'desc')->paginate(50);
        // return PostCollection::collection($posts);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function create($committee)
    {
        dd($committee);
        # code...
    }

    public function store(Request $request, $committeeId)
    {
        $committee = Committee::findOrFail($committeeId);

  $validator = Validator::make($request->all(), [
              'body' => 'required|string|min:2',
        ]);
 if ($validator->fails()) {

         return response()->json(['errors'=>$validator->errors()]);
        }
        $vol = Volunteer::where('user_id',auth()->user()->id)->first();
        $volPos = $committee->volunteer()->where('vol_id',$vol->id)->value('position');
        if ($volPos == null) {
            return response()->json(['error'=> 'you are not in this committee']);
        }
        elseif($volPos != 'volunteer'){
       $committee->post()->create(
            [
                'body' => $request->body,
                'created_at' =>now(),
                'creator' => $vol->id,
                'status_id' => Status::where('name','approved')->value('id'),

        ]);
    }
        else
        {
             $committee->post()->create(
            [

                'body' =>$request->body,
                'status_id' => Status::where('name','pending')->value('id'),
                'created_at' =>now(),
                'creator' => auth()->user()->id,
        ]);
    }
        // event(new PostEvent($post));
        return response()->json(['success' => 'Done successfully']);
    }

    public function show(Post $post)
    {
        // $post = Post::findOrFail($id);
        return new PostResource($post);
    }
    public function edit(Post $post)
    {
        if ($post->creator == JWTAuth::parseToken()->authenticate()->id) {
                    return new PostResource($post);

        }
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return PostResource
     */
    public function update(Request $request, Post $post)
    {
        dd($post);
        // $post = Post::findOrFail($id);

        if ($post->creator == JWTAuth::parseToken()->authenticate()->id) {
             $validator = Validator::make($request->all(), [
              'body' => 'required|string|min:2',
        ]);
             if ($validator->fails()) {

         return response()->json(['errors'=>$validator->errors()]);
        }
            $post->body = $request->input('body');
            $post->update();
            return response()->json(['success' => 'updated successfully']);
        } else {
            return response()->json(['error' => 'Un Authenticated']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy( Post $post)
    {
        // $post = Post::findOrFail($id);

        if ($post->user_id == JWTAuth::parseToken()->authenticate()->id) {
            Comment::where('post_id', $id)->delete();
            $post->delete();
            return response()->json(['success' => 'deleted successfully']);
        } else {
            return response()->json(['error' => 'Un Authenticated']);
        }
    }

}
