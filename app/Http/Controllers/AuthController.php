<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/auth/register",
     *     summary="Register a new user",
     *     tags={"auth"},
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="password_confirmation",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=201, description="User successfully registered"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'message' => 'User successfully registered',
//            'user' => $user,
//            'token' => $token,
        ], 201);
    }

    /**
     * @OA\Post(
     *     path="/api/auth/login",
     *     summary="Log in a user",
     *      tags={"auth"},
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string", example="admin@example.com")
     *     ),
     *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string", example="123123")
     *     ),
     *     @OA\Response(response=200, description="Login successful"),
     *     @OA\Response(response=201, description="User already logged in"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function login(Request $request)
    {
        if ($request->session()->has('user_id')) {
            return response()->json(['error' => 'User already logged in'], 201);
        }

        $credentials = $request->only('email', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = JWTAuth::user();
        $request->session()->put('user_id', $user->id);
        $request->session()->put('is_admin', ($user->is_admin == 1));
        $request->session()->put('jwt_token', $token);

        return $this->respondWithToken($token);
    }

    /**
     * @OA\Post(
     *     path="/api/auth/refresh",
     *     summary="Refresh a JWT token",
     *      tags={"auth"},
     *     @OA\Response(response=200, description="Token refreshed"),
     *     @OA\Response(response=400, description="Invalid token"),
     *     @OA\Response(response=401, description="Token not provided")
     * )
     */
    public function refresh(Request $request)
    {
        $this->authorizeUser($request);
        $newToken = JWTAuth::refresh();
        $request->session()->put('jwt_token', $newToken);
        return $this->respondWithToken($newToken);
    }

    /**
     * @OA\Post(
     *     path="/api/auth/logout",
     *     summary="Log out a user",
     *      tags={"auth"},
     *     @OA\Response(response=200, description="Successfully logged out"),
     *     @OA\Response(response=400, description="Invalid token"),
     *     @OA\Response(response=401, description="Token not provided")
     * )
     */
    public function logout(Request $request)
    {
        $this->authorizeUser($request);
        JWTAuth::invalidate();
        $request->session()->forget(['user_id', 'jwt_token']);
        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Return a response with the token.
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60
        ]);
    }
}
