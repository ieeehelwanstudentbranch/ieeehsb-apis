<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Events\PostEvent;
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
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {

        $this->validate($request, [
            'body' => 'required',
        ]);

        $post = new Post;
        $post->body = $request->input('body');
        $post->created_at = now();
        $post->user_id = auth()->user()->id;
        $post->save();
        event(new PostEvent($post));
        return response()->json(['success' => 'Done successfully']);
    }

    public function show($id)
    {
        $post = Post::findOrFail($id);
        return new PostResource($post);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return PostResource
     */
    public function update(Request $request, $id)
    {
        $post = Post::findOrFail($id);

        if ($post->user_id == JWTAuth::parseToken()->authenticate()->id) {
            $this->validate($request, [
                'body' => 'required',
            ]);
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
    public function destroy($id)
    {
        $post = Post::findOrFail($id);

        if ($post->user_id == JWTAuth::parseToken()->authenticate()->id) {
            Comment::where('post_id', $id)->delete();
            $post->delete();
            return response()->json(['success' => 'deleted successfully']);
        } else {
            return response()->json(['error' => 'Un Authenticated']);
        }
    }

}
