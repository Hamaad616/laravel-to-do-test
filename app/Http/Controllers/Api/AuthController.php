<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Services\EmailVerificationService;
use App\Transformers\LoginTransformer;
use App\Transformers\LogoutTransformer;
use App\Transformers\UnauthorizedUserTransformer;
use Exception;
use Illuminate\Http\JsonResponse;
use Spatie\Fractal\Fractal;

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
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
        $this->emailVerificationService = $emailVerificationService;
    }

    /**
     * @throws Exception
     */
    public function register(RegisterRequest $request){

        //validate the required data
        //it is a good practice to always validate data to keep app from attacks


        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => \Hash::make($request->password)
        ]);

        // after creation,
        // I have created an email verification service that implements an interface
        // the EmailVerification Service is injected here i.e. using Dependency Injection
        return $this->emailVerificationService->generateOtp($user);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return JsonResponse
     */
    public function login(LoginRequest $request)
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth('api')->attempt($credentials)) {
            $data = ['error' => 'Invalid email or password provided.'];
            $transformedResponse = Fractal::create()->item($data)->transformWith(new UnauthorizedUserTransformer())->toArray();
            return response()->json($transformedResponse, 401);
        }

        // Get the authenticated user
        $authenticatedUser = auth('api')->user();

        $transformedUser = Fractal::create()->item($this->respondWithToken($token, $authenticatedUser))->transformWith(new LoginTransformer())->toArray();
        return response()->json($transformedUser);
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
        $message = 'Successfully logged out';
        $transformed_response = Fractal::create()->item($message)->transformWith(new LogoutTransformer())->toArray();
        return response()->json($transformed_response);
    }

    /**
     * Get the token array structure.
     *
     * @param string $token
     * @param $user
     * @return array
     */
    protected function respondWithToken(string $token, $user)
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => $user
        ];
    }
}
