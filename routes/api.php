<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TweetsController;
use App\Http\Controllers\UsersController;
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
Route::post('auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);

Route::group(['middleware' => ['auth:api']], function () {
    //Authentication
    Route::get('/auth/user', [AuthController::class, 'authUser']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/auth/change-password', [AuthController::class, 'changePassword']);

    //Following
    Route::get('/users/suggested-following', [UsersController::class, 'suggestedFollowing']);
    Route::get('/users/{id}/following', [UsersController::class, 'followed']);
    Route::post('/users/{id}/following', [UsersController::class, 'following']);
    Route::post('/users/{id}/unfollow', [UsersController::class, 'unfollow']);

    //Users
    Route::get('/users/all', [UsersController::class, 'all']);
    Route::apiResource('users', UsersController::class)->except(['create', 'edit']);

    //Followers
    Route::get('/users/{id}/followers', [UsersController::class, 'followers']);

    //tweets
    Route::get('/tweets/users-followed-tweets', [TweetsController::class, 'followedTweets']);
    Route::post('/tweets/{id}/images', [TweetsController::class, 'storeImage']);
    Route::delete('/tweets/{id}/images/{image}', [TweetsController::class, 'deleteImage']);
    Route::apiResource('tweets', TweetsController::class)->except(['create', 'edit']);
});
