<?php

namespace App\Http\Controllers;

use App\Comment;
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

    }

    public function index()
    {
        $posts = Post::orderBy('created_at', 'desc')->paginate(50);
        return PostCollection::collection($posts);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {

            $this->validate($request, [
                'title' => 'required',
                'body' => 'required',
            ]);

            $post = new Post;
            $post->title = $request->input('title');
            $post->body = $request->input('body');
            $post->user_id = auth()->user()->id;
            $post->save();

            return redirect('api/articles')->with('success', 'Done successfully');
    }

    public function show($id)
    {
        $post = Post::findOrFail($id);
        return new PostResource($post);
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
        $post =   Post::findOrFail($id);

        if($post->user_id == JWTAuth::parseToken()->authenticate()->id) {
            $this->validate($request, [
                'title' => 'required',
                'body' => 'required',
            ]);

            $post->title = $request->input('title');
            $post->body = $request->input('body');
            $post->update();

            return new PostResource($post);
        }else{
            return response()->json('Un Authenticated');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post =   Post::findOrFail($id);

        if($post->user_id == JWTAuth::parseToken()->authenticate()->id) {
        Comment::where('post_id',$id)->delete();
            $post->delete();
            return redirect('/api/articles')->with('success', 'Done successfully');

        }else{
            return response()->json('Un Authenticated');
        }
    }

}
