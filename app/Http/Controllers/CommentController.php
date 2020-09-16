<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Volunteer;
use App\Http\Resources\Post\CommentsCollection;
use App\Http\Resources\Post\PostResource;
use App\Post;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class CommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.auth');
        $this->middleware('type:volunteer');


    }

//    view comments
    public function index($id)
    {
        $post = Post::query()->findOrFail($id);
        $comments = Comment::query()->where('post_id', $post->id)->get();
        return CommentsCollection::collection($comments);
    }

    // add Commment
    public function store(Request $request, $id)
    {
         $validator = Validator::make($request->all(), [
              'body' => 'required|string|min:2',
        ]);

        if ($validator->fails()) {
         return response()->json(['errors'=>$validator->errors()]);
        }
            $vol = Volunteer::where('user_id',JWTAuth::parseToken()->authenticate()->id)->first();
        if ($vol)
        {
            $comment = new Comment();
            $comment->body = $request->body;
            $comment->post_id = $id;
            $comment->creator = $vol->id;
            $comment->created_at = now();
            $comment->save();
            return response()->json(['success' => 'Comment Added Successfully']);
        }
         else {
            return response()->json(['error' => 'Un Authenticated']);
        }
    }

    //updateComment
    public function update(Request $request,Comment $comment)
    {
        // $comment = Comment::findOrFail($id);
        $vol = Volunteer::findOrFail($comment->creator);
        if ($vol->user_id == JWTAuth::parseToken()->authenticate()->id) {
            $validator = Validator::make($request->all(), [
              'body' => 'nullable|string|min:2',
        ]);
             if ($validator->fails()) {

         return response()->json(['errors'=>$validator->errors()]);
        }
            $comment->body = $request->body;
            $comment->update();
            return response()->json(['success' => 'Comment Updated Successfully']);
        } else {
            return response()->json(['error' => 'Un Authenticated']);
        }
    }

    //delete comment
    public function destroy(Comment $comment)
    {
        // $comment = Comment::findOrFail($id);
        $vol = Volunteer::findOrFail($comment->creator);
        if ($vol->user_id == JWTAuth::parseToken()->authenticate()->id) {
            $comment->delete();
            return response()->json(['success' => 'Comment Deleted Successfully']);
        } else {
            response()->json(['error' => 'Un Authenticated']);
        }
    }
}
