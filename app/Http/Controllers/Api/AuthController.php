<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * Summary of AuthController
 */
class AuthController extends Controller
{
    //

    public function __construct(){
        $this->middleware('auth:api', ['except' => ['signup', 'login', 'refreshToken']]);
    }

    public function signup(RegisterRequest $request){
        $validated = $request->validated();

        $user = User::create(array_merge(
            $validated,
            ['password' => bcrypt($request->password)]
        ));
        return response(compact('user'), Response::HTTP_CREATED);
    }

    
    public function login(AuthRequest $request){
        $request->validated();
        $credentials = $request->safe()->only('email', 'password');
        $token = Auth::guard('api')->attempt($credentials);
        $type = 'Bearer';

        if (!$token) {
            return response([
                "message" => "Invalid Credentials"
            ], Response::HTTP_FORBIDDEN);
        }

        $user = Auth::guard('api')->user();
        return response(compact('token', 'type', 'user'), 200);
    }

    public function logout(Request $request){
        Auth::guard('api')->logout();
        return response([
            'message' => 'Logout Succesfully'
        ], 200);
    }

    public function refreshToken(Request $request){
        $token = Auth::guard('api')->refresh();
        $type = 'Bearer';
        return response(compact('token', 'type'), 200);
    }

    /**
     * Return user by jwt token      
     * @param Request $request send by client
     * @return \Illuminate\Contracts\Auth\Authenticatable|null User that identifies by jwt token
     */
    public function getUser(Request $request){
        return Auth::guard('api')->user();
    }
}
