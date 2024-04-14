<?php

namespace App\Http\Controllers\v2\Users;

use App\Http\Controllers\Controller;
use App\Models\v2\Users\User;
use App\Models\v2\Users\UserCapacitor;
use Illuminate\Http\Request;

class UserCapacitorController extends Controller
{
    public function show(Request $request, $id)
    {
        $user_id = $id;
        $role = 'employee';
        $user = User::where('id', $id)->first();
        $user_token = $request->query('user_token');
        if ($user) {
            $role = $user->access_roles != null ? $user->access_roles->name : null;
            if ($role == 'admin') {
                $role = 'hr';
            } elseif ($role == 'manager') {
                $role = 'manager';
            } else {
                $role = 'employee';
            }
        }
        $recent_token = UserCapacitor::where('user_id', $user_id)->first();
        if (! $recent_token) {
            UserCapacitor::updateOrCreate(
                ['user_id' => $user_id],
                ['user_token' => $user_token]
            );
            $new_token = UserCapacitor::where('user_id', $user_id)->first();

            return response()->json([
                'data' => [
                    'company_token' => config('company_token'),
                    'user_token' => $new_token['user_token'],
                    'user_role' => $role,
                ],
            ], 200);
        }

        return response()->json([
            'data' => [
                'company_token' => config('company_token'),
                'user_token' => $recent_token['user_token'],
                'user_role' => $role,
            ],
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $user_id = $id;
        $user_token = $request->query('user_token');
        if (! $user_token || $user_token == null) {
            return;
        }
        UserCapacitor::updateOrCreate(
            ['user_id' => $user_id],
            ['user_token' => $user_token]
        );

    }

    public function destroy($id)
    {
        $token = UserCapacitor::where('user_id', $id)->delete();
        if (! $token) {
            return response()->json(['data' => null], 204);
        }

        return response()->json(['data' => null], 204);
    }
}
