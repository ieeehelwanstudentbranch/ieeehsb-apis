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
        if ($post = Post::query()->find($id)) {
            $comments = Comment::query()->where('post_id', $post->id)->get();
            return CommentsCollection::collection($comments);
        }
        else{
            return response()->json([
                'Error' => 'The Post is not found']);
        }
    }

    // add Commment
    public function store(Request $request, $id)
    {
        if ($post = Post::query()->find($id)) {

            $validator = Validator::make($request->all(), [
                'body' => 'required|string|min:2',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()]);
            }
            $vol = Volunteer::where('user_id', JWTAuth::parseToken()->authenticate()->id)->first();
            if ($vol) {
                $comment = new Comment();
                $comment->body = $request->body;
                $comment->post_id = $id;
                $comment->creator = $vol->id;
                $comment->created_at = now();
                $comment->save();
                return response()->json([
                    'response' => 'Success',
                    'message' => 'Comment Has Been Added Successfully',
                ]);
            } else {
                return response()->json([
                    'response' => 'Error',
                    'message' => 'Un Authenticated',
                ]);
            }
        } else {
            return response()->json([
                'Error' => 'The Post is not found']);
        }
    }
    //updateComment
    public function update(Request $request,$id)
    {
         if($comment = Comment::find($id)) {
             $vol = Volunteer::find($comment->creator);
             if ($vol->user_id == JWTAuth::parseToken()->authenticate()->id) {
                 $validator = Validator::make($request->all(), [
                     'body' => 'nullable|string|min:2',
                 ]);
                 if ($validator->fails()) {

                     return response()->json(['errors' => $validator->errors()]);
                 }
                 $comment->body = $request->body != null ? $request->body : $comment->body;
                 $comment->update();
                 return response()->json([
                     'response' => 'Success',
                     'message' => 'Comment Has Been Updated Successfully',
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
                 'message' => 'Comment Not  found',
             ]);
         }
    }

    //delete comment
    public function destroy($id)
    {
        $comment = Comment::find($id);
        if ($comment = Comment::find($id)) {
            $vol = Volunteer::find($comment->creator);
            $postCreator = Volunteer::find($comment->post->creator);
            if ($vol->user_id == JWTAuth::parseToken()->authenticate()->id ||
                $postCreator->user_id == JWTAuth::parseToken()->authenticate()->id) {
                $comment->delete();
                return response()->json([
                    'response' => 'Success',
                    'message' => 'Comment Has Been Deleted Successfully',
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
                'message' => 'Comment not found',
            ]);
        }
    }

}
