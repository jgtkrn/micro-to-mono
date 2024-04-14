<?php

namespace App\Http\Controllers\v2\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\v2\Auth\LoginRequest;
use App\Http\Requests\v2\Auth\ResetPasswordRequest;
use App\Http\Resources\v2\Users\UserResource;
use App\Models\v2\Users\User;
use App\Traits\ResponseWithError;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    use ResponseWithError;

    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return $this->responseWithError(400, __('The provided credentials are incorrect'));
        }

        if ($user->user_status == false) {
            return $this->responseWithError(400, __('This user is inactive'));
        }

        $token = $user->createToken($request->device_name)->plainTextToken;

        return response()->json([
            'data' => [
                'token' => $token,
                'user' => $user,
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        $user->tokens()->delete();

        return response(null, 204);
    }

    public function authUser(Request $request)
    {
        return response()->json([
            'data' => new UserResource($request->user()),
        ]);
    }

    public function forgotPassword(Request $request)
    {
        $request->merge(['email_cityu' => $request->email]);
        $request->validate(['email_cityu' => 'required|email']);
        $isUserExist = User::where('email_cityu', $request->email_cityu)->count();

        if (! $isUserExist) {
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
            ],
        ]);
    }

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
            ],
        ]);
    }

    public function getResetPassword(Request $request)
    {
        $token = $request->query('token');
        $email = $request->query('email');
        $url = config('auth.app_send_domain') . "/auth/reset-password?token={$token}&email={$email}";

        return redirect($url);
    }
}
