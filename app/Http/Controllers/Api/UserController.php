<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;

class UserController extends Controller
{
    //

    public function __construct(){
        $this->middleware('auth:api');
    }

    public function changePassword(Request $request){
        $user = Auth::guard('api')->user();
        $validator = \Validator::make($request->all(), [
            'old_password' => 'required',
            'new_password' => 'required',
        ]);

        if($validator->fails())
            return $validator->errors();
        
        $user->password = \Hash::make($request->new_password);
        $user->save();

        return response([
            'message' => 'Password change successfully'
        ]);
    }

    public function updateUser(Request $request){
        $user = Auth::guard('api')->user();

        $validator = \Validator::make($request->all(), [
            'username' => 'nullable|max:50',
            'email' => 'nullable|email',
            'first_name' => 'nullable',
            'last_name' => 'nullable',
            'profile_img' => 'image|nullable'
        ]);

        if($validator->fails())
            return $validator->errors();

        $validate_data = $validator->validate();
        $path_image = $this->storeProfileImg($request, $user, 'profile_img');
        if($path_image)
            $validate_data['profile_img'] = $path_image;

        $user->update($validate_data);
        $state = 'Update successfully';
        return response(compact('user', 'state'), 200);
    }

    public function deleteUser(Request $request){
        $user = Auth::guard('api')->user();
        $user->delete();
        $state = 'Delete User successfully';
        return response(compact('user', 'state'), 200);
    }

    public function getPosts(Request $request){
        $user = Auth::guard('api')->user();
        if(!$user)
            return response([ 'message' => 'User not found'], 401);
        return $user->posts;
    }

    /**
     * 
     */
    private function storeProfileImg(Request $request, User $user = null, string $field_name){
        if(!$request->hasFile($field_name) || !$user)
            return;

        $file = $request->file($field_name);
        $name = sprintf("%s-%s.%s", $user->username, Carbon::now()->toDateTimeString(), $file->extension());
        $path = $file->storeAs('profile-images', $name);
        return $path;
    }
}
