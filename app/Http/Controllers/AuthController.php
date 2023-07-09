<?php

namespace App\Http\Controllers;

use App\Http\Requests\Users\ChangePasswordFormRequest;
use App\Http\Requests\Users\LoginFormRequest;
use App\Http\Requests\Users\RegisterFormRequest;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
	protected $userRepository;

	public function __construct(UserRepository $userRepository)
	{
		$this->userRepository = $userRepository;
	}

    /**
    *   @OA\Post(
    *       path="/auth/login",
    *       summary="Login",
    *       description="Returns user info and access token",
    *       description="Login by email and password",
    *       operationId="authLogin",
    *       tags={"Authentication"},
    *       @OA\RequestBody(
    *           required=true,
    *           description="Pass user credentials",
    *           @OA\JsonContent(
    *               required={"email","password"},
    *               @OA\Property(property="email", type="string", example="johndoe@sample.com"),
    *               @OA\Property(property="password", type="string", format="password", example="johndoe123"),
    *           ),
    *       ),
    *       @OA\Response(
    *           response=200,
    *           description="Success",
    *       ),
    *       @OA\Response(
    *           response=422,
    *           description="Unauthorized",
    *           @OA\JsonContent(
    *               @OA\Property(property="message", type="string", example="Not authorized"),
    *           )
    *       ),
    *   )
    */
    public function login(LoginFormRequest $request)
    {
        if (auth()->attempt($request->only(['email', 'password']))) {
            $token = auth()->user()->createToken(config('app.name', 'Laravel'))->accessToken;
            return response()->json([
                'token' => $token,
                'user' => auth()->user()
            ], 200);
        } else {
            return response()->json(['error' => 'Unauthorized'], 422);
        }
    }

    /**
    *   @OA\Post(
    *       path="/auth/register",
    *       summary="Registration",
    *       description="User Registration",
    *       operationId="authRegister",
    *       tags={"Authentication"},
    *       @OA\RequestBody(
    *           required=true,
    *           description="Pass registration form",
    *           @OA\JsonContent(
    *               required={"name", "email","password", "password_confirmation"},
    *               @OA\Property(property="name", type="string", example="John Doe"),
    *               @OA\Property(property="email", type="string", example="johndoe@sample.com"),
    *               @OA\Property(property="password", type="string", format="password", example="johndoe123"),
    *               @OA\Property(property="password_confirmation", type="string", format="password", example="johndoe123"),
    *           ),
    *       ),
    *       @OA\Response(
    *           response=200,
    *           description="Registration successful",
    *       ),
    *       @OA\Response(
    *           response=422,
    *           description="Unprocessable Entity",
    *       ),
    *   )
    */
    public function register(RegisterFormRequest $request)
    {
        $this->userRepository->store($request);
   		
        return response()->json([
            'message' => 'Registration successful'
        ], 200);
    }

    /**
    *   @OA\Get(
    *       path="/auth/user",
    *       summary="Profile",
    *       description="User Profile",
    *       operationId="authUser",
    *       tags={"Authentication"},
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
    public function authUser()
    {
        return response()->json([
            'data'  => auth()->user()
        ], 200);
    }

    /**
    *   @OA\POST(
    *       path="/auth/logout",
    *       summary="Logout",
    *       description="Logout User",
    *       operationId="authLogout",
    *       tags={"Authentication"},
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
    public function logout()
    {
        auth()->user()->token()->revoke();
        
        return response()->json([
            'message'  => 'You have successfully logged out!'
        ], 200);
    }

    /**
     * change a user password.
     * @responseFile storage/responses/users/user.register.json
     * @param  \Illuminate\Http\Request  $request
     * @response status=422 scenario="invalid input" {"message": "Validation failed"}
     * @return \Illuminate\Http\Response
     */
    public function changePassword(ChangePasswordFormRequest $request)
    {        
        return response()->json([
            'message' => 'Changed password successful',
            'user'    => $this->userRepository->changePassword($request)
        ], 202);
    }
}
