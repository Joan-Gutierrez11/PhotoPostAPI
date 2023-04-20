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
            return response([ 'message' => 'User not logged. ' ], 401);

        $validator = \Validator::make($request->all(), [
            'title' => 'max:50', 'description' => 'nullable', 'image' => 'image|nullable',
        ]);

        if($validator->fails())
            return $validator->errors();

        $validate_data = $validator->validate();


        $post = Post::create(array_merge(
            $validate_data,
            [ 'user_id' => $user->id ]
        ));

        $filename = sprintf("%d/%s", $post->id, $post->title);
        $post->image = $this->storeImage($request, $filename,'image');
        $post->save();

        return response([ 'message' => 'Post created' ], 201);
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
            return response(['message' => 'User not logged.'], 401);

        $validator = \Validator::make($request->all(), [
            'title' => 'nullable|max:50', 'description' => 'nullable', 'image' => 'image|nullable',
        ]);

        if($validator->fails())
            return $validator->errors();

        $validate_data = $validator->validate();
        
        $filename = sprintf("%s/%d-%s", $user->username, $post->id, $post->title);
        $path_image_post = $this->storeImage($request, $filename, 'image');
        if($path_image_post)
            $validate_data['image'] = $path_image_post;

        $post->update($validate_data);
        return $post;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        //
    }

    private function storeImage(Request $request, string $filename, string $field_name){
        if(!$request->hasFile($field_name) || !$filename)
            return;

        $file = $request->file($field_name);
        $new_name = $filename . '.' . $file->extension();
        $path = $file->storeAs('post-image', $new_name);
        return $path;
    }
}
