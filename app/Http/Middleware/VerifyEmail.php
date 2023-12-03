<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyEmail
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (JsonResponse) $next
     */
    public function handle(Request $request, Closure $next)
    {
        if($request->user() && !$request->user()->email_verified_at){
            return response()->json(['error' => 'Unverified user. Please verify your email.'], 403);
        }
        return $next($request);
    }
}
