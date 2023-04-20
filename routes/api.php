<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::prefix('/auth')->group(function(){
    Route::post('/signup', [App\Http\Controllers\Api\AuthController::class, 'signup']);
    Route::post('/login', [App\Http\Controllers\Api\AuthController::class, 'login']);
    Route::post('/logout', [App\Http\Controllers\Api\AuthController::class, 'logout']);
    Route::post('/refresh', [App\Http\Controllers\Api\AuthController::class, 'refreshToken']);
    Route::get('/user', [App\Http\Controllers\Api\AuthController::class, 'getUser']);
});

Route::prefix('/user')->group(function(){
    Route::post('/change-password', [App\Http\Controllers\Api\UserController::class, 'changePassword']);
    Route::put('/update', [App\Http\Controllers\Api\UserController::class, 'updateUser']);
    Route::delete('/delete', [App\Http\Controllers\Api\UserController::class, 'deleteUser']);
    Route::get('/posts', [App\Http\Controllers\Api\UserController::class, 'getPosts']);
});

Route::prefix('/posts')->controller(App\Http\Controllers\Api\PostController::class)->group(function(){
    Route::get('', 'index');
    Route::get('/{post}', 'show');
    Route::post('', 'store');
    Route::put('/{post}', 'update');
});
