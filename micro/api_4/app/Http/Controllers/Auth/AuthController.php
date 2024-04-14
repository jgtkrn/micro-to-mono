<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\UserCapacitor;
use App\Traits\ResponseWithError;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Auth")
 */
class AuthController extends Controller
{
    use ResponseWithError;

    public function __construct()
    {
        $this->middleware('auth:sanctum')->only(['authUser', 'logout']);
    }

    /**
     * @OA\Post(
     *     path="/user-api/v1/auth/login",
     *     operationId="v1Login",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="email", type="string", format="email", example="john.snow@stark.com"),
     *              @OA\Property(property="password", type="string", example="secret"),
     *              @OA\Property(property="device_name", type="string", example="web")
     *          )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Login success",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="data",
     *                  type="object",
     *                  @OA\Property(property="token", type="string"),
     *                  @OA\Property(property="user", type="object", ref="#/components/schemas/User")
     *              ),
     *          )
     *     ),
     *     @OA\Response(
     *          response=400,
     *          description="Login failed",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="status",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="400"),
     *                  @OA\Property(property="message", type="string", example="The provided credentials are incorrect"),
     *                  @OA\Property(property="errors", type="array", example="[]", @OA\Items(type="integer")),
     *              )
     *          )
     *     )
     * )
     *
     * @param LoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->responseWithError(400, __('The provided credentials are incorrect'));
        }

        if ($user->user_status == false){
            return $this->responseWithError(400, __('This user is inactive'));
        }

        $token = $user->createToken($request->device_name)->plainTextToken;

        return response()->json([
            'data' => [
                'token' => $token,
                'user' => $user,
            ]
        ]);
    }

    /**
     * @OA\Post(
     *     path="/user-api/v1/auth/logout",
     *     operationId="v1Logout",
     *     tags={"Auth"},
     *     @OA\Response(
     *          response=204,
     *          description="Logout success"
     *     )
     * )
     *
     * @param LoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $user = $request->user();
        $capacitorToken = UserCapacitor::where('user_id', $user->id);
        if($capacitorToken){
            $capacitorToken->delete();
        }
        $user->tokens()->delete();
        return response(null, 204);
    }

    /**
     * @OA\Get(
     *     path="/user-api/v1/auth/user",
     *     operationId="v1GetAuthUser",
     *     tags={"Auth"},
     *     @OA\Response(
     *          response=200,
     *          description="Authenticated user",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="data",
     *                  type="object",
     *                  @OA\Property(property="user", type="object", ref="#/components/schemas/User")
     *              ),
     *          )
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="Unauthenticated.")
     *          )
     *     )
     * )
     *
     * @param LoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function authUser(Request $request)
    {
        return response()->json([
            'data' => new UserResource($request->user()),
        ]);
    }

    /**
     * @OA\Post(
     *     path="/user-api/v1/auth/password/forgot",
     *     operationId="v1ForgotPassword",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="email", type="string", format="email", example="john.snow@stark.com")
     *          )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Reset password link sended",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="data",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="200"),
     *                  @OA\Property(property="message", type="string", example="Password reset link has been sended to your email.")
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *          response=422,
     *          description="Email validation fail",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="status",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="422"),
     *                  @OA\Property(property="message", type="string", example=""),
     *                  @OA\Property(property="errors", type="array", example="[]", @OA\Items(type="integer")),
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *          response=500,
     *          description="Fail to send reset password link",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="status",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="500"),
     *                  @OA\Property(property="message", type="string", example="Fail to send reset password link"),
     *                  @OA\Property(property="errors", type="array", example="[]", @OA\Items(type="integer")),
     *              )
     *          )
     *     )
     * )
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function forgotPassword(Request $request)
    {
        $request->merge(['email_cityu' => $request->email]);
        $request->validate(['email_cityu' => 'required|email']);
        $isUserExist = User::where('email_cityu', $request->email_cityu)->count();

        if (!$isUserExist) {
            return $this->responseWithError(500, __('The provided credentials are incorrect'));
        }

        $status = Password::sendResetLink($request->only('email_cityu'));

        if ($status !== Password::RESET_LINK_SENT) {
            return $this->responseWithError(500, 'Fail to send reset password link');
        }

        return response()->json([
            'data' => [
                'code' => 200,
                'message' => 'Password reset link has been sended to your email.',
            ]
        ]);
    }

    /**
     * @OA\Post(
     *     path="/user-api/v1/auth/password/reset",
     *     operationId="v1ResetPassword",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="token", type="string", example="TWFuIGlzIGRpc3Rpbmd1aXNoZWQsIG5vdCBvbmx5IGJ5IGhpcyByZWFzb24sIGJ1dCAuLi4="),
     *              @OA\Property(property="email", type="string", format="email", example="john.snow@stark.com"),
     *              @OA\Property(property="password", type="string", example="ourSecret"),
     *              @OA\Property(property="password_confirmation", type="string", example="ourSecret")
     *          )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Password reset success",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="data",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="200"),
     *                  @OA\Property(property="message", type="string", example="Password reset success.")
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *          response=422,
     *          description="Email validation fail",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="status",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="422"),
     *                  @OA\Property(property="message", type="string", example=""),
     *                  @OA\Property(property="errors", type="array", example="[]", @OA\Items(type="integer")),
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *          response=500,
     *          description="Fail to send reset password",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="status",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="500"),
     *                  @OA\Property(property="message", type="string", example="Fail to send reset password"),
     *                  @OA\Property(property="errors", type="array", example="[]", @OA\Items(type="integer")),
     *              )
     *          )
     *     )
     * )
     *
     * @param \App\Http\Requests\ResetPasswordRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        $status = Password::reset(
            $request->only('token', 'email', 'password', 'password_confirmation'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => bcrypt($password),
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            return $this->responseWithError(500, 'Fail to send reset password');
        }

        return response()->json([
            'data' => [
                'code' => 200,
                'message' => 'Password reset success.',
            ]
        ]);
    }

    /**
     * @OA\Get(
     *     path="/user-api/v1/auth/password/reset",
     *     operationId="v1GetResetPassword",
     *     tags={"Auth"},
     *     @OA\Parameter(
     *         name="token",
     *         in="query",
     *         description="sent token",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="valid email",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="User successfully redirected",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="data",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="200"),
     *                  @OA\Property(property="message", type="string", example="User data has been redirected.")
     *              )
     *          )
     *     ),
     * )
     *
     * @param \App\Http\Requests\ResetPasswordRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getResetPassword(Request $request)
    {
        $token = $request->query('token');
        $email = $request->query('email');
        $url = env('APP_SEND_DOMAIN')."/auth/reset-password?token={$token}&email={$email}";
        return redirect($url);
    }
}
