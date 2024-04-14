<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class IsAll
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
        switch ($request->access_role) {
            case 'admin':
                return $next($request);
                break;

            case 'manager':
                return $next($request);
                break;

            case 'user':
                return $next($request);
                break;
            
            default:
                return response()->json(['error' => 'Unauthorized.'], 401);
                break;
        }
    }
}
