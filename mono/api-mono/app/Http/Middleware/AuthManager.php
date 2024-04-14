<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthManager
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $data = $request->user();
        if (! $data) {
            return response()->json([
                'data' => null,
                'status' => [
                    'code' => 401,
                    'message' => 'Unauthorized',
                    'errors' => [],
                ],
            ], 401);
        }
        //append request
        $isBzn = false;
        $isCga = false;
        $isOther = true;
        $isHcsw = false;
        $isHcw = false;
        if (count($data['teams']) > 0) {
            $teams_code = collect($data['teams'])->pluck('code')->toArray();
            if (in_array('bzn', $teams_code)) {
                $isBzn = true;
                $isOther = false;
            }
            if (in_array('cga', $teams_code)) {
                $isCga = true;
                $isOther = false;
            }
            if (in_array('hc-sw', $teams_code)) {
                $isHcsw = true;
                $isOther = false;
            }
            if (in_array('hc-w', $teams_code)) {
                $isHcw = true;
                $isOther = false;
            }
            if (in_array('operation', $teams_code)) {
                $isBzn = true;
                $isCga = true;
                $isHcsw = true;
                $isHcw = true;
                $isOther = false;
            }
        }
        $request->merge([
            'user_id' => $data->id,
            'user_name' => $data->name,
            'user_role' => $data->accessRoles != null ? $data->accessRoles->name : null,
            'user_teams' => $data->teams,
            'access_role' => $data->accessRoles != null ? $data->accessRoles->name : null,
            'is_bzn' => $isBzn,
            'is_cga' => $isCga,
            'is_hcsw' => $isHcsw,
            'is_hcw' => $isHcw,
            'is_other' => $isOther,
        ]);

        return $next($request);
    }
}
