<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Chapter;
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
use Illuminate\Support\Facades\DB;


class PostController extends Controller
{

    public function __construct()
    {
        $this->middleware('jwt.auth');
        $this->middleware('type:volunteer');


    }
    public function chapterVols($chapterId)
    {
        $chapter = Chapter::findOrFail($chapterId);
            $chapterVols = array();
            foreach ($chapter->committee as $key => $comm) {
                foreach ($comm->volunteer as $key => $vol) {
                    array_push($chapterVols, $vol->id);
                }
            }
            array_push($chapterVols, $chapter->chairperson_id);
            return $chapterVols;
    }

    public function index($id)
    {
        if(Chapter::find($id) != null)
        {
            $chapter = Chapter::findOrFail($id);
            
            $chapterVols = self::chapterVols($id);

            $vol = Volunteer::where('user_id',JWTAuth::parseToken()->authenticate()->id)->first();
            if (in_array($vol->id,$chapterVols) ||$vol->position->name =='chairperson' || $vol->position->name == 'vice-chairperson') {
                $approved = Status::where('name','approved')->value('id');
                 $posts = $chapter->post()->where('status_id',$approved)->orderBy('created_at', 'desc')->paginate(50);
                 return PostCollection::collection($posts);
            }
        }
        else{

        $committee = Committee::findOrFail($id);

            $vol = Volunteer::where('user_id',JWTAuth::parseToken()->authenticate()->id)->first();
        $volPos = $committee->volunteer()->where('vol_id',$vol->id)->value('position');
    //Anyone in the committeee and the chairperson and the vice can see the posts
       if($volPos != null || ($vol->position->name == 'chairperson' || ($vol->position->name == 'vice-chairperson'))){
         
            $approved = Status::where('name','approved')->value('id');
            $posts = $committee->post()->where('status_id',$approved)->orderBy('created_at', 'desc')->paginate(50);
            return PostCollection::collection($posts);
         }
         else{
                return response()->json('you are not in comm post page');
         }


        // $posts = Post::orderBy('created_at', 'desc')->paginate(50);
        // return PostCollection::collection($posts);
}
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function create($committee)
    {
        # code...
    }

    public function store(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
              'body' => 'required|string|min:2',
        ]);

        if ($validator->fails()) {
         return response()->json(['errors'=>$validator->errors()]);
        }

        if(Chapter::find($id) != null)
        {
            $chapter = Chapter::findOrFail($id);
            
            $chapterVols = self::chapterVols($id);
            $vol = Volunteer::where('user_id',JWTAuth::parseToken()->authenticate()->id)->first();
            if ($vol->position->name =='chairperson' ||$vol->position->name == 'vice-chairperson' || $vol->id == $chapter->chairperson_id) {
 
            $chapter->post()->create(
            [
                'body' => $request->body,
                'created_at' =>now(),
                'creator' => $vol->id,
                'status_id' => Status::where('name','approved')->value('id'),

            ]);
             return response()->json('Post Created Successfully');

        }
        elseif(in_array($vol->id,$chapterVols))
        {
             $chapter->post()->create(
            [

                'body' =>$request->body,
                'status_id' => Status::where('name','pending')->value('id'),
                'created_at' =>now(),
                'creator' => $vol->id,
            ]);            
             return response()->json('The Post is sent to the chairperson to be approved');
         }

        }
        else{

        $committee = Committee::findOrFail($id);
        $vol = Volunteer::where('user_id',JWTAuth::parseToken()->authenticate()->id)->first();
        $volPos = $committee->volunteer()->where('vol_id',$vol->id)->value('position');
        //anyone exept the comm volunteers and the chair / vice 
        if ( $volPos != 'volunteer' &&($vol->position->name != 'chairperson' ||
            ($vol->position->name != 'vice-chairperson'))) {
            return response()->json(['error'=> 'you are not in this committee']);
        }
        /*the cahirperson and vice can add posts in this committee
        Any Volunteer in the committee can add post but it will be sent to director to approve it
        */
        elseif($vol->position->name == 'chairperson' || ($vol->position->name == 'vice-chairperson' ||($volPos == 'director')))
        {
            $committee->post()->create(
            [
                'body' => $request->body,
                'created_at' =>now(),
                'creator' => $vol->id,
                'status_id' => Status::where('name','approved')->value('id'),

            ]);
             return response()->json('Post Created Successfully');

        }
        else
        {
            $committee->post()->create(
            [

                'body' =>$request->body,
                'status_id' => Status::where('name','pending')->value('id'),
                'created_at' =>now(),
                'creator' => $vol->id,
            ]);
            return response()->json('The Post is sent to the director to be approved');

         }
        // event(new PostEvent($post));
        }
    }

    public function show(Post $post)
    {
        $vol = Volunteer::findOrFail($post->creator);
        if ($vol->user_id == JWTAuth::parseToken()->authenticate()->id) {
        return new PostResource($post);
    }
     else{
            return response()->json('you arenot allowed to see this post');

         }
     }

    public function edit(Post $post)
    {
        $vol = Volunteer::findOrFail($post->creator);
        if ($vol->user_id == JWTAuth::parseToken()->authenticate()->id) {
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
        // $post = Post::findOrFail($id);
        $vol = Volunteer::findOrFail($post->creator);
        if ($vol->user_id == JWTAuth::parseToken()->authenticate()->id) {
             $validator = Validator::make($request->all(), [
              'body' => 'nullable|string|min:2',
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
        $vol = Volunteer::findOrFail($post->creator);
        if ($vol->user_id == JWTAuth::parseToken()->authenticate()->id) {
            Comment::where('post_id', $id)->delete();
            $post->delete();
            return response()->json(['success' => 'deleted successfully']);
        } else {
            return response()->json(['error' => 'Un Authenticated']);
        }
    }
    public function pendingPost($id)
    {
        if(Chapter::find($id) != null)
        {
            $chapter = Chapter::findOrFail($id);
            
            $chapterVols = self::chapterVols($id);
            $vol = Volunteer::where('user_id',JWTAuth::parseToken()->authenticate()->id)->first();
            if ($vol->id == $chapter->chairperson_id) {
                $pending = Status::where('name','pending')->value('id');
                $posts = $chapter->post()->where('status_id',$pending)
                ->orderBy('created_at', 'desc')->paginate(50);
                return PostCollection::collection($posts);
            }
        }
       else
       {
            $committee = Committee::findOrFail($id);
            $vol = Volunteer::where('user_id',JWTAuth::parseToken()->authenticate()->id)->first();
            $volPos = $committee->volunteer()->where('vol_id',$vol->id)->value('position');
            //anyone exept the comm volunteers and the chair / vice 
            if ($vol->position->name == 'director')
            {
                $pending = Status::where('name','pending')->value('id');
                $posts = $committee->post()->where('status_id',$pending)
                ->orderBy('created_at', 'desc')->paginate(50);
                return PostCollection::collection($posts);
            }
        }
    }
    public function approvePost( Request $request)
    {
         $validator = Validator::make($request->all(), [
              'post' => 'required|numeric',
        ]);
             if ($validator->fails()) {
         return response()->json(['errors'=>$validator->errors()]);
        }
        $approved = Status::where('name','approved')->value('id');
        $post = Post::findOrFail($request->post);
        $post->status_id = $approved;
        $post->update();
        return response()->json('the post has been approved');

    }
    public function disapprovePost( Request $request)
    {
         $validator = Validator::make($request->all(), [
              'post' => 'required|numeric',
        ]);
             if ($validator->fails()) {
         return response()->json(['errors'=>$validator->errors()]);
        }
        $post = Post::findOrFail($request->post);
        $post->delete();
        return response()->json('the post has been deleted');
    }
}