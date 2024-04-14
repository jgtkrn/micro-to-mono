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
            return response($response->body(), $response->status());
        }

        $data = $response->collect('data');
        //append request
        $request->merge([
            'user_id' => $data['id'],
            'user_name' => $data['name'],
            'access_role' => $data['access_role']
        ]);

        return $next($request);
    }
}
