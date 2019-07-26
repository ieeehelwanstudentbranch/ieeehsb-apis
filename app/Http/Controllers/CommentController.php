<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Http\Resources\Post\PostResource;
use App\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class CommentController extends Controller
{
    // add Commment
    public function addComment(Request $request , $id ){
        if ($request->isMethod('post')){
            $this->validate($request, [
                'comment_body' => 'required',
            ]);
            $comment = new Comment();
            $comment->comment_body = $request->input('comment_body');
            $comment->created_at =now();
            $comment->post_id = $id ;
            $comment->user_id = JWTAuth::parseToken()->authenticate()->id ;
            $comment->save();
        }
        $post = Post::findOrFail($id);
        return new PostResource($post);
    }

    //update comment
    public function updateComment(Request $request,$id){
        if ($request->isMethod('post')) {
            $this->validate($request, [
                'comment_body' => 'required',
            ]);
            $comment = Comment::findOrFail($id);
            $pid = $comment->post_id;
            $comment->comment_body = $request->input('comment_body');
            $comment->update();
            $post = Post::findOrFail($pid);
            return new PostResource($post);
        }
    }

    //delete comment
    public function destroyComment($id){
        $comment = Comment::findOrFail($id);
        $pid = $comment->post_id;
        $comment->delete();
        $post = Post::findOrFail($pid);
        return new PostResource($post);
    }
}