<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Chapter;
use App\Committee;
use App\Position;
use App\Season;
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
        if ($chapter = Chapter::find($chapterId)) {

            $chapterVols = array();
            foreach ($chapter->committee as $key => $comm) {
                foreach ($comm->volunteer as $key => $vol) {
                    array_push($chapterVols, $vol->id);
                }
            }
            array_push($chapterVols, $chapter->chairperson_id);
            $chapterVols = array_unique($chapterVols);
            return $chapterVols;
        }
        else{
            return response()->json([
                'response' => 'Error',
                'message' => 'Chapter Not Found',
            ]);
        }
    }
    public function CommVols($commId)
    {
        if($committee = Committee::find($commId))
        {
        $commVols = array();
            foreach ($committee->volunteer as $key => $vol) {
                array_push($commVols, $vol->id);
        }
//            $director = DB::table('vol_committees')->where('committee_id',$commId)
//                ->where('position','director')
//                ->where('season_id',Season::where('isActive',1)->value('id'))->select('vol_id')->first();
//            array_push($commVols, $director->vol_id);
            return $commVols;
    }
        else{
            return response()->json([
                'response' => 'Error',
                'message' =>  'Committee Not Found',
            ]);
        }
    }
    public function getChapPost($id)
    {
        if ($chapter = Chapter::find($id)) {
            $chapterVols = self::chapterVols($id);

            $vol = Volunteer::where('user_id', JWTAuth::parseToken()->authenticate()->id)->first();
            if (in_array($vol->id, $chapterVols) || $vol->position->name == 'chairperson' || $vol->position->name == 'vice-chairperson') {
                $approved = Status::where('name', 'approved')->value('id');
                $posts = $chapter->post()->where('status_id', $approved)->orderBy('created_at', 'desc')->paginate(50);
                return PostCollection::collection($posts);
            }
            else{
                return response()->json([
                    'response' => 'Error',
                    'message' => 'You are not allowed to see this page',
                ]);
            }
        } else {
            return response()->json([
                'response' => 'Error',
                'message' => 'Chapter Not Found',
            ]);
        }
    }

    public function getCommPost($id)
    {
        if( $committee = Committee::find($id))
        {
        $commVols = self::commVols($id);

            $vol = Volunteer::where('user_id',JWTAuth::parseToken()->authenticate()->id)->first();
        $volPos = $committee->volunteer()->where('vol_id',$vol->id)->value('position');
    //Anyone in the committeee and the chairperson and the vice can see the posts
       if(in_array($vol->id,$commVols) || ($vol->position->name == 'chairperson' || ($vol->position->name == 'vice-chairperson'))){

            $approved = Status::where('name','approved')->value('id');
            $posts = $committee->post()->where('status_id',$approved)->orderBy('created_at', 'desc')->paginate(50);
            return PostCollection::collection($posts);
         }
         else{
             return response()->json([
                 'response' => 'Error',
                 'message' =>  'You are not allowed to see this page',
             ]);
         }
        // $posts = Post::orderBy('created_at', 'desc')->paginate(50);
        // return PostCollection::collection($posts);
}
        else{
            return response()->json([
                'response' => 'Error',
                'message' =>  'Committee Not Found',
            ]);
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

    public function storeGeneralPost( Request $request)
    {
        $validator = Validator::make($request->all(), [
            'body' => 'required|string|min:2',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        $vol = Volunteer::where('user_id', JWTAuth::parseToken()->authenticate()->id)->first();

            $post = new Post;
            $post->body = $request->body;
            $post->created_at = now();
            $post->creator = $vol->id;
            $post->post_type = 'general';
            $post->post_id = 0;
            if ($vol->position->name == 'chairperson' || $vol->position->name == 'vice-chairperson')
            {
                $post->status_id = Status::where('name', 'approved')->value('id');
                $post->save();
                return response()->json([
                    'response' => 'Success',
                    'message' =>  'Post Created Successfully',
                ]);

            } else {
                $post->status_id = Status::where('name', 'pending')->value('id');
                $post->save();
                return response()->json([
                    'response' => 'Success',
                    'message' =>  'The Post is sent to the chairperson to be approved',
                ]);

            }


    }
    public function postGeneral()
    {
        $posts = Post::where('post_type','general')->get();
        return PostCollection::collection($posts);

    }
    public function pendingGeneralPost()
    {
        $vol = Volunteer::where('user_id',JWTAuth::parseToken()->authenticate()->id)->first();
//        if ($vol->position->name =='chairperson' ||$vol->position->name == 'vice-chairperson') {
            $staus = Status::where('name', 'pending')->value('id');
            $posts = Post::where('post_type', 'general')->where('status_id', $staus)->get();
            return PostCollection::collection($posts);
//        }
//        else{
            return response()->json([
                'response' => 'Error',
                'message' =>  'You are not allowed to see this page',
            ]);
//        }
    }
    public function storeChapPost(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'body' => 'required|string|min:2',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()]);
        }
        if ($chapter = Chapter::find($id))
        {
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
                return response()->json([
                    'response' => 'Success',
                    'message' =>  'Post Has Been Created Successfully',
                ]);

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
                return response()->json([
                    'response' => 'Warning',
                    'message' =>  'The Post Is Sent To The Chairperson To Be Approved',
                ]);
            }

            else{
                return response()->json([
                    'response' => 'Error',
                    'message' =>  'You are not allowed to create this post',
                ]);
            }

        }
        else{
                return response()->json([
                    'response' => 'Error',
                    'message' =>  'Chapter Not Found',
                ]);
        }
    }
    public function storeCommPost(Request $request, $id)
    {

        if(Committee::find($id) == null) {
            return response()->json([
                'response' => 'Error',
                'message' => 'Committee Not Found',
            ]);
        }
        else
        {
        $committee = Committee::findOrFail($id);
        $chapterChair = $committee->chapter->chairperson_id; //chairperson

        $vol = Volunteer::where('user_id',JWTAuth::parseToken()->authenticate()->id)->first();
        $volPos = $committee->volunteer()->where('vol_id',$vol->id)->value('position');
        //anyone exept the comm volunteers and the chair / vice

        /*the cahirperson and vice can add posts in this committee
        Any Volunteer in the committee can add post but it will be sent to director to approve it
        */
        if ($vol->position->name != 'chairperson' &&
            $vol->position->name != 'vice-chairperson' && $volPos != 'volunteer' && $chapterChair == null) {
                return response()->json([
                    'response' => 'Error',
                    'message' =>  'You Are Not Volunteer In This Committee',
                ]);
            }
        elseif($vol->position->name == 'chairperson' ||
            $vol->position->name == 'vice-chairperson' ||
            $volPos == 'director' || $vol->id == $chapterChair)
        {
            dd('s');
            $validator = Validator::make($request->all(), [
                'body' => 'required|string|min:2',
            ]);
            if ($validator->fails()) {
                return response()->json(['errors'=>$validator->errors()]);
            }
            $committee->post()->create(
            [
                'body' => $request->body,
                'created_at' =>now(),
                'creator' => $vol->id,
                'status_id' => Status::where('name','approved')->value('id'),

            ]);
            return response()->json([
                'response' => 'Success',
                'message' =>  'Post Created Successfully',
            ]);

        }

        else
        {
            $validator = Validator::make($request->all(), [
                'body' => 'required|string|min:2',
            ]);
            if ($validator->fails()) {
                return response()->json(['errors'=>$validator->errors()]);
            }
            $committee->post()->create(
            [

                'body' =>$request->body,
                'status_id' => Status::where('name','pending')->value('id'),
                'created_at' =>now(),
                'creator' => $vol->id,
            ]);
            return response()->json([
                'response' => 'Warning',
                'message' =>  'The Post is sent to the director to be approved',
            ]);

         }
        // event(new PostEvent($post));
        }
    }

    public function show($p)
    {
//        $post = Post::find($p);
        if ($post = Post::find($p)) {
            $vol = Volunteer::findOrFail($post->creator);
            if ($vol->user_id == JWTAuth::parseToken()->authenticate()->id) {
                return new PostResource($post);
            }
            else{
                return response()->json([
                    'response' => 'Error',
                    'message' =>  'You Are Not Allowed To See This Post',
                    ]);
            }
        }
        else {
            return response()->json([
                'response' => 'Error',
                'message' =>  'Post Not Found',
            ]);
        }
    }


    public function edit($p)
    {
        $post = Post::find($p);
        if ($post = Post::find($p)) {

            $vol = Volunteer::findOrFail($post->creator);
            if ($vol->user_id == JWTAuth::parseToken()->authenticate()->id) {
                return new PostResource($post);
            }
        }
        else {
            return response()->json([
                'response' => 'Error',
                'message' =>  'Post Not Found',
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $p)
    {
         if ($post = Post::find($p)) {
             $vol = Volunteer::findOrFail($post->creator);
             if ($vol->user_id == JWTAuth::parseToken()->authenticate()->id || $vol->position->name == 'chairperson') {
                 $validator = Validator::make($request->all(), [
                     'body' => 'nullable|string|min:2',
                 ]);
                 if ($validator->fails()) {

                     return response()->json(['errors' => $validator->errors()]);
                 }

                 $post->body = $request->body != null ? $request->body : $post->body;
                 $post->update();
                 return response()->json([
                     'response' => 'Success',
                     'message' => 'The Post Is Updated Successfully',
                 ]);
             } else {
                 return response()->json([
                     'response' => 'Error',
                     'message' => 'Un Authenticated',
                 ]);
             }
         }
         else{
             return response()->json([
                 'response' => 'Error',
                 'message' =>  'Post Not Found',
             ]);
         }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($p)
    {
        // $post = Post::findOrFail($id);
        if ($post = Post::find($p)) {
            $vol = Volunteer::findOrFail($post->creator);
            if ($vol->user_id == JWTAuth::parseToken()->authenticate()->id) {
                Comment::where('post_id', $post->id)->delete();
                $post->delete();
                return response()->json([
                    'response' => 'Success',
                    'message' =>  'The Post Has Been Deleted Successfully',
                    ]);
            } else {
                return response()->json([
                'response' => 'Error',
                'message' =>  'Un Authenticated',
                 ]);
            }
         }
        else{
            return response()->json([
                'response' => 'Error',
                'message' =>  'Post Not Found',
                ]);
        }
    }
    public function pendingChapPost($id)
    {
        if(Chapter::find($id) != null) {
            $chapter = Chapter::findOrFail($id);

            $chapterVols = self::chapterVols($id);
            $vol = Volunteer::where('user_id', JWTAuth::parseToken()->authenticate()->id)->first();
            if ($vol->position->name == 'chairperson' || $vol->position->name == 'vice-chairperson' || $vol->id == $chapter->chairperson_id) {

                $pending = Status::where('name', 'pending')->value('id');
                $posts = $chapter->post()->where('status_id', $pending)
                    ->orderBy('created_at', 'desc')->paginate(10);
                $p = PostCollection::collection($posts);
                if ($p->isEmpty())
                {
                    return response()->json([
                        'response' => 'Error',
                        'message' => 'No Pending Posts',
                    ]);
                }
                else{
                    return $p;
                }
            } else {
                return response()->json([
                    'response' => 'Error',
                    'message' => 'You Are Not Allowed To See This Page',
                ]);
            }

        }
        else{
            return response()->json([
                'response' => 'Error',
                'message' =>  'Chapter Not Found',
            ]);
        }
    }
        public function pendingCommPost($id)
        {
            if ($committee = Committee::find($id))
            {
            $vol = Volunteer::where('user_id',JWTAuth::parseToken()->authenticate()->id)->first();
            $volPos = $committee->volunteer()->where('vol_id',$vol->id)->value('position');
            //anyone exept the comm volunteers and the chair / vice
           if ($vol->position->name =='chairperson' || $vol->position->name == 'vice-chairperson' || $vol->position->name == 'director'
           || $vol->id == $committee->chapter->chairperson_id)
            {
                $pending = Status::where('name','pending')->value('id');
                $posts = $committee->post()->where('status_id',$pending)
                ->orderBy('created_at', 'desc')->paginate(50);
                $p = PostCollection::collection($posts);
                if ($p->isEmpty())
                {
                    return response()->json([
                        'response' => 'Error',
                        'message' => 'No Pending Posts',
                    ]);
                }
                else{

                    return $p;
                }
            }
            else{
                return response()->json([
                    'response' => 'Error',
                    'message' =>  'You Are Not Allowed To See This Page',
                ]);
            }
        }
            else{
                return response()->json([
                    'response' => 'Error',
                    'message' =>  'Committee Not Found',
                ]);
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
             if ($post = Post::find($request->post)) {
                 if ($post->status_id == $approved) {
                     return response()->json([
                         'response' => 'Error',
                         'message' => 'The Post Is Approved Before',
                     ]);
                 } else {
                     $post->status_id = $approved;
                     $post->update();
                     return response()->json([
                         'response' => 'Success',
                         'message' => 'The Post Has Been Approved',
                     ]);
                 }
             }
             else{
                 return response()->json([
                     'response' => 'Error',
                     'message' => 'The Post Is Not Found',
                 ]);
             }


    }
    public function disapprovePost( Request $request)
    {
         $validator = Validator::make($request->all(), [
              'post' => 'numeric|required',
        ]);
             if ($validator->fails()) {
         return response()->json(['errors'=>$validator->errors()]);
        }
        if ($post = Post::find($request->post)) {

            $post->delete();
                $post->update();
            return response()->json([
                'response' => 'Success',
                'message' =>  'The Post Has Been Deleted',
            ]);
            }
        else {
            return response()->json([
                'response' => 'Error',
                'message' => 'The Post Is Not Found',
            ]);
        }
    }
}
