<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OtpVerificationRequest;
use App\Models\Otp;
use App\Services\EmailVerificationService;
use App\Transformers\OtpVerificationTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Fractal\Fractal;

class OtpController extends Controller
{
    private EmailVerificationService $emailVerificationService;

    /**
     * @param EmailVerificationService $emailVerificationService
     */
    public function __construct(EmailVerificationService $emailVerificationService)
    {
        $this->middleware('auth:api');
        $this->middleware('verify_email', ['except' => 'verifyOtp']);
        $this->emailVerificationService = $emailVerificationService;
    }


    /**
     * @param OtpVerificationRequest $request
     * @return JsonResponse
     */
    public function verifyOtp(OtpVerificationRequest $request): JsonResponse
    {

        //here I am checking for most recent OTP for current user
        $otp_user = Otp::where('user_id', \Auth::guard('api')->user()->id)->where('verified', false)->first();

        //if it exists I then call my service
        // In service there is a method named verifyOtp it takes the Authenticated User and the otp entered by the user
        if($otp_user){
            $result =  $this->emailVerificationService->verifyOtp(\Auth::user(), $request->otp);
        }else{
            $result = [
                'message' => 'Please request a new OTP, this OTP has been expired'
            ];
        }

        $transformedResponse = Fractal::create()->item($result)->transformWith(new OtpVerificationTransformer())->toArray();

        return response()->json($transformedResponse);
    }
}
