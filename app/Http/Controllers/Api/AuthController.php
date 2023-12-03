<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Otp;
use App\Models\User;
use App\Services\EmailVerificationService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 *
 */
class AuthController extends Controller
{

    private EmailVerificationService $emailVerificationService;

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct(EmailVerificationService $emailVerificationService)
    {
        $this->emailVerificationService = $emailVerificationService;
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    /**
     * @throws Exception
     */
    public function register(Request $request){

        //validate the required data
        //it is a good practice to always validate data to keep app from attacks
        $request->validate([
            "name" => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'confirm_password' => 'required|same:password',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => \Hash::make($request->password)
        ]);

        //after creation
        // I have created an email verification service that implements an interface
        // the EmailVerification Service is injected here i.e. using Dependency Injection
        return $this->emailVerificationService->generateOtp($user);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return JsonResponse
     */
    public function login()
    {
        \request()->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $credentials = request(['email', 'password']);

        if (! $token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return JsonResponse
     */
    public function logout()
    {
        auth()->invalidate();
        auth()->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        $request->validate([
            'otp' => 'required'
        ]);

        //here I am checking for most recent OTP for current user
        $otp_user = Otp::where('otp', $request->otp)->where('user_id', \Auth::user()->id)->where('verified', false)->first();

        //if it exists I then call my service
        // In service there is a method named verifyOtp it takes the Authenticated User and the otp entered by the user
        if($otp_user){
            return $this->emailVerificationService->verifyOtp(\Auth::user(), $request->otp);
        }else{
            return response()->json([
                'message' => 'Please request a new OTP, this OTP has been expired'
            ]);
        }
    }
}
