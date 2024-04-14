<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class ExternalAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $response = Http::acceptJson()->withToken($request->bearerToken())->get(env('USER_SERVICE_API_URL') . '/auth/user');
        if (!$response->ok()) {
            return response()->json([
                "status" => [
                    "code" => 401,
                    "message" => "",
                    "errors" => [
                        [
                            "message" => "Unauthorized"
                        ]
                    ]
                ]
            ], 401);
        }

        $data = $response->collect('data');
        //append request
        $isBzn = false;
        $isCga = false;
        $isOther = true;
        $isHcsw = false;
        $isHcw = false;
        if(count($data['teams']) > 0){
            $teams_code = collect($data['teams'])->pluck('code')->toArray();
            if(in_array('bzn', $teams_code)){
                $isBzn = true;
                $isOther = false;
            }
            if(in_array('cga', $teams_code)){
                $isCga = true;
                $isOther = false;
            }
            if(in_array('hc-sw', $teams_code)){
                $isHcsw = true;
                $isOther = false;
            }
            if(in_array('hc-w', $teams_code)){
                $isHcw = true;
                $isOther = false;
            }
            if(in_array('operation', $teams_code)){
                $isBzn = true;
                $isCga = true;
                $isHcsw = true;
                $isHcw = true;
                $isOther = false;
            }
        }
        $request->merge([
            'user_id' => $data['id'],
            'user_name' => $data['name'],
            'user_role' => $data['access_role'],
            'user_teams' => $data['teams'],
            'access_role' => $data['access_role'],
            'is_bzn' => $isBzn,
            'is_cga' => $isCga,
            'is_hcsw' => $isHcsw,
            'is_hcw' => $isHcw,
            'is_other' => $isOther
        ]);

        return $next($request);
    }
}
