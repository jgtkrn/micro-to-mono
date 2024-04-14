<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use OpenApi\Annotations as OA;


class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->only(['authUser', 'logout']);
    }


    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'error' => [
                    'code' => 401,
                    'message' => 'The provided credentials are incorrect.',
                ]
            ], 400);
        }

        $token = $user->createToken($request->device_name)->plainTextToken;

        return response()->json([
            'data' => [
                'token' => $token,
                'user' => $user,
            ]
        ]);
    }


    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response(null, 204);
    }


    public function authUser(Request $request)
    {
        return response()->json([
            'data' => $request->user(),
        ]);
    }
}
