<?php

namespace App\Http\Middleware;

use App\Transformers\VerifyOtpTransformer;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Fractal\Fractal;
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
        // Check if the request is for the verifyOtp method
        if ($request->route()->getActionMethod() === 'verifyOtp') {
            // If it is, allow the request without email verification
            return $next($request);
        }

        if($request->user() && !$request->user()->email_verified_at){
            $transformedResponse = Fractal::create()->item(['error' => 'Unverified user. Please verify your email.'])->transformWith(new VerifyOtpTransformer())->toArray();
            return response()->json($transformedResponse, 403);
        }
        return $next($request);
    }
}
