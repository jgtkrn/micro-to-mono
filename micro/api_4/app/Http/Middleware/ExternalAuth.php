<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;

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
        $user = $request->user();
        $user->access_role = $user->access_roles == null? null : $user->access_roles->name;
        $response = $user;
        if (!$response) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        //append request
        $request->merge([
            'user_id' => $response['id'],
            'user_name' => $response['name'],
            'access_role' => $response['access_role']
        ]);

        return $next($request);
    }
}
