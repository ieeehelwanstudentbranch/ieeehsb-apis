<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Http\Resources\Post\CommentsCollection;
use App\Http\Resources\Post\PostResource;
use App\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class CommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.auth');

    }

//    view comments
    public function index($id)
    {
        $post = Post::query()->findOrFail($id);
        $comments = Comment::query()->where('post_id', $post->id)->get();
        return CommentsCollection::collection($comments);
    }

    // add Commment
    public function addComment(Request $request, $id)
    {
        if ($request->isMethod('post')) {
            $this->validate($request, [
                'comment_body' => 'required',
            ]);
            $comment = new Comment();
            $comment->comment_body = $request->input('comment_body');
            $comment->created_at = now();
            $comment->post_id = $id;
            $comment->user_id = JWTAuth::parseToken()->authenticate()->id;
            $comment->save();
            return response()->json(['success' => 'Comment Added Successfully']);
        } else {
            return response()->json(['error' => 'Un Authenticated']);
        }
    }

    //update comment
    public function updateComment(Request $request, $id)
    {
        $comment = Comment::findOrFail($id);
        if ($request->isMethod('PUT') && $comment->user_id == JWTAuth::parseToken()->authenticate()->id) {
            $this->validate($request, [
                'comment_body' => 'required',
            ]);
            $comment->comment_body = $request->input('comment_body');
            $comment->update();
            return response()->json(['success' => 'Comment Updated Successfully']);
        } else {
            return response()->json(['error' => 'Un Authenticated']);
        }
    }

    //delete comment
    public function destroyComment($id)
    {
        $comment = Comment::findOrFail($id);
        if ($comment->user_id == JWTAuth::parseToken()->authenticate()->id) {
            $comment->delete();
            return response()->json(['success' => 'Comment Deleted Successfully']);
        } else {
            response()->json(['error' => 'Un Authenticated']);
        }
    }
}