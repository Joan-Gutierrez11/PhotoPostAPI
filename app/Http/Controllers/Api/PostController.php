<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{

    public function __construct(){}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Post::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = \Auth::guard('api')->user();
        if(!$user)
            return response([
                'message' => 'User not logged. '
            ], 401);


        Post::create(array_merge(
            $request->all(),
            ['user_id' => $user->id]
        ));
        return response([
            'message' => 'Post created'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        $user_owner = $post->user->only('username', 'email');   
        return response(array_merge(
            $post->toArray(),
            compact('user_owner')
        ), 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        $user = \Auth::guard('api')->user();
        if(!$user)
            return response([
                'message' => 'User not logged. '
            ], 401);

        $post->update($request->all());
        return $post;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        //
    }
}
