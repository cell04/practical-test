<?php

namespace App\Http\Controllers;

use App\Http\Requests\Users\FollowingUserRequest;
use App\Http\Requests\Users\IndexUserRequest;
use App\Http\Requests\Users\StoreUserRequest;
use App\Http\Requests\Users\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    protected $userRepository;

	public function __construct(UserRepository $userRepository)
	{
		$this->userRepository = $userRepository;
	}

    /**
    *   @OA\Get(
    *       path="/users",
    *       summary="Users Index",
    *       description="List of user",
    *       operationId="indexUser",
    *       tags={"Users"},
    *       @OA\Response(
    *           response=200,
    *           description="Success"
    *       ),
    *       @OA\Response(
    *           response=401,
    *           description="Returns when user is not authenticated",
    *           @OA\JsonContent(
    *               @OA\Property(property="message", type="string", example="Not authorized"),
    *           )
    *       )
    *   )
    */
    public function index(IndexUserRequest $request)
    {
        return UserResource::collection(
            $this->userRepository->paginateWithFilters(
                $request,
                $request->per_page,
                $request->order_by
            )
        );
    }


    /**
    *   @OA\Post(
    *       path="/users",
    *       summary="Users Store",
    *       description="Create new user",
    *       operationId="storeUser",
    *       tags={"Users"},
    *       @OA\RequestBody(
    *           required=true,
    *           description="Pass user credentials",
    *           @OA\JsonContent(
    *               required={"name", "email","password", "password_confirmation"},
    *               @OA\Property(property="name", type="string", example="John Doe"),
    *               @OA\Property(property="email", type="string", example="johndoe@sample.com"),
    *               @OA\Property(property="password", type="string", format="password", example="johndoe123"),
    *               @OA\Property(property="password_confirmation", type="string", format="password", example="johndoe123"),
    *           ),
    *       ),
    *       @OA\Response(
    *           response=201,
    *           description="Success",
    *       ),
    *       @OA\Response(
    *           response=422,
    *           description="Unauthorized",
    *       ),
    *   )
    */
    public function store(StoreUserRequest $request)
    {
        return response()->json([
            'message' => 'Resource successfully stored',
            'user' => $this->userRepository->store($request)
        ], 201);
    }

    /**
    *   @OA\Get(
    *   path="/users/{id}",
    *   description="Show detail of user",
    *       summary="Users Show",
    *   operationId="showUser",
    *   tags={"Users"},
    *       @OA\Parameter(
    *           in="path",
    *           name="id",
    *           required=true,
    *           @OA\Schema(type="number"),
    *           @OA\Examples(example="id", value="1", summary="An ID value."),
    *       ),
    *       @OA\Response(
    *           response=200,
    *           description="Success",
    *       ),
    *   )
    */  
    public function show($id)
    {
        return response()->json([
            'message' => 'Resource successfully retrieve',
            'user' => $this->userRepository->findOrFail($id)
        ], 200);
    }

    /**
    *   @OA\PATCH(
    *       path="/users/{id}",
    *       summary="Users Update",
    *       description="Update user",
    *       operationId="updateUser",
    *       tags={"Users"},
    *       @OA\Parameter(
    *           in="path",
    *           name="id",
    *           required=true,
    *           @OA\Schema(type="number"),
    *           @OA\Examples(example="id", value="1", summary="An ID value."),
    *       ),
    *       @OA\RequestBody(
    *           required=true,
    *           description="Pass user credentials",
    *           @OA\JsonContent(
    *               required={"name"},
    *               @OA\Property(property="name", type="string", example="John Doe"),
    *           ),
    *       ),
    *       @OA\Response(
    *           response=202,
    *           description="Success",
    *       ),
    *       @OA\Response(
    *           response=422,
    *           description="Unauthorized",
    *       ),
    *   )
    */
    public function update(UpdateUserRequest $request, $id)
    {
        $this->userRepository->update($request, $id);

        return response()->json([
            'message' => 'Resource successfully updated'
        ], 202);
    }

    /**
    *   @OA\Delete(
    *   path="/users/{id}",
    *   description="Users delete",
    *       summary="Users Delete",
    *   operationId="deleteUser",
    *   tags={"Users"},
    *       @OA\Parameter(
    *           in="path",
    *           name="id",
    *           required=true,
    *           @OA\Schema(type="number"),
    *           @OA\Examples(example="id", value="1", summary="An ID value."),
    *       ),
    *       @OA\Response(
    *           response=202,
    *           description="Success",
    *       ),
    *   )
    */  
    public function destroy($id)
    {
        $this->userRepository->destroy($id);

        return response()->json([
            'message' => 'Resource successfully deleted'
        ], 202);
    }

    /**
    *   @OA\Get(
    *       path="/users/all",
    *       description="Get all users",
    *       summary="Users All",
    *       operationId="getAllUser",
    *       tags={"Users"},
    *       @OA\Response(
    *           response=200,
    *           description="Success",
    *       ),
    *   )
    */
    public function all()
    {
        return response()->json([
            'message' => 'Resource successfully retrieve',
            'users'=> $this->userRepository->all()
        ], 200);
    }

    /**
    *   @OA\POST(
    *       path="/users/{id}/following",
    *       summary="Users Following",
    *       description="Following user",
    *       operationId="followingUser",
    *       tags={"Users Following"},
    *       @OA\Parameter(
    *           in="path",
    *           name="id",
    *           required=true,
    *           @OA\Schema(type="number"),
    *           @OA\Examples(example="id", value="1", summary="An ID value."),
    *       ),
    *       @OA\RequestBody(
    *           required=true,
    *           description="Pass user credentials",
    *           @OA\JsonContent(
    *               required={"following_id"},
    *               @OA\Property(property="following_id", type="number", example="5"),
    *           ),
    *       ),
    *       @OA\Response(
    *           response=202,
    *           description="Success",
    *       ),
    *       @OA\Response(
    *           response=422,
    *           description="Unauthorized",
    *       ),
    *   )
    */
    public function following(FollowingUserRequest $request, $id)
    {
        $this->userRepository->following($request, $id);

        return response()->json([
            'message' => 'Resource successfully followed'
        ], 202);
    }

    /**
    *   @OA\POST(
    *       path="/users/{id}/unfollow",
    *       summary="Users Unfollow",
    *       description="Unfollow user",
    *       operationId="unfollowUser",
    *       tags={"Users Following"},
    *       @OA\Parameter(
    *           in="path",
    *           name="id",
    *           required=true,
    *           @OA\Schema(type="number"),
    *           @OA\Examples(example="id", value="1", summary="An ID value."),
    *       ),
    *       @OA\RequestBody(
    *           required=true,
    *           description="Pass user credentials",
    *           @OA\JsonContent(
    *               required={"following_id"},
    *               @OA\Property(property="following_id", type="number", example="5"),
    *           ),
    *       ),
    *       @OA\Response(
    *           response=202,
    *           description="Success",
    *       ),
    *       @OA\Response(
    *           response=422,
    *           description="Unauthorized",
    *       ),
    *   )
    */
    public function unfollow(FollowingUserRequest $request, $id)
    {
        $this->userRepository->unfollow($request, $id);

        return response()->json([
            'message' => 'Resource successfully unfollowed'
        ], 202);
    }

    /**
    *   @OA\Get(
    *   path="/users/{id}/followers",
    *   description="Show detail of users follower",
    *       summary="Users Follower",
    *   operationId="showUserFollower",
    *   tags={"Users Followers"},
    *       @OA\Parameter(
    *           in="path",
    *           name="id",
    *           required=true,
    *           @OA\Schema(type="number"),
    *           @OA\Examples(example="id", value="1", summary="An ID value."),
    *       ),
    *       @OA\Response(
    *           response=200,
    *           description="Success",
    *       ),
    *   )
    */  
    public function followers($id)
    {
        return response()->json([
            'message' => 'Resource successfully retrieve',
            'user' => $this->userRepository->followers($id)
        ], 200);
    }

    /**
    *   @OA\Get(
    *   path="/users/{id}/following",
    *   description="Show detail of users following",
    *       summary="Users Following",
    *   operationId="showUserFollowing",
    *   tags={"Users Following"},
    *       @OA\Parameter(
    *           in="path",
    *           name="id",
    *           required=true,
    *           @OA\Schema(type="number"),
    *           @OA\Examples(example="id", value="1", summary="An ID value."),
    *       ),
    *       @OA\Response(
    *           response=200,
    *           description="Success",
    *       ),
    *   )
    */  
    public function followed($id)
    {
        return response()->json([
            'message' => 'Resource successfully retrieve',
            'followed' => $this->userRepository->followed($id)
        ], 200);
    }

    /**
    *   @OA\Get(
    *   path="/users/suggested/following",
    *   description="Show the list of users suggested following",
    *       summary="Users Suggested Following",
    *   operationId="showUserSuggestedFollowing",
    *   tags={"Users Following"},
    *       @OA\Response(
    *           response=200,
    *           description="Success",
    *       ),
    *   )
    */  
    public function suggestedFollowing()
    {
        return response()->json([
            'message' => 'Resource successfully retrieve',
            'followed' => $this->userRepository->suggestedFollowing()
        ], 200);
    }
}
