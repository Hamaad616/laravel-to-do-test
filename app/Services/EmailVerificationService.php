<?php

namespace App\Services;

use App\Interfaces\EmailVerificationServiceInterface;
use App\Models\Otp;
use App\Models\User;
use App\Notifications\EmailVerificationNotification;
use App\Transformers\RegisterationTransformer;
use App\Transformers\RegisterExceptionTransformer;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Spatie\Fractal\Fractal;

class EmailVerificationService implements EmailVerificationServiceInterface
{


    /**
     * @param User $user
     * @param $otp
     * @return JsonResponse
     */
    function generateEmail(User $user, $otp): JsonResponse
    {
        // This method uses laravel's built in notify method for Eloquent
        // I created EmailVerificationNotification and passed it to the notify method

        // I am looking for any exception thrown by either mail client
        // or by the error inside notify

        try {
            $user->notify(new EmailVerificationNotification('Please use this code to verify your account', 'Email Verification', $user, $otp));
            $transformedData = Fractal::create()->item($user)->transformWith(new RegisterationTransformer())->toArray();

            return response()->json($transformedData);
        }catch (\Exception $exception){
            $transformedData = Fractal::create()->item($exception)->transformWith(new RegisterExceptionTransformer())->toArray();

            return response()->json($transformedData, 500);
        }
    }

    /**
     * @param User $user
     * @return JsonResponse
     * @throws \Exception
     */
    function generateOtp(User $user): JsonResponse
    {
        // This functions generates a 6 digit random digits
        // and then send it to user's email

        $random_digits = random_int(0, 1000000);

        $otp = new Otp();
        $otp->user_id = $user->id;
        $otp->otp = $random_digits;
        $otp->generated_at = Carbon::now();
        $otp->save();

        return $this->generateEmail($user, $random_digits);
    }

    /**
     * @param User $user
     * @param int $otp_digits
     * @return JsonResponse
     */
    function verifyOtp(User $user, int $otp_digits): JsonResponse
    {
        // here I simply get the recent OTP
        $recent_user_otp = $user->recent_otp;

        // check for match
        if($recent_user_otp->otp !== $otp_digits){
            return response()->json([
                'message' => 'OTP provided is invalid, please provide valid OTP'
            ]);
        }

        // if matches I expire the OTP and mark it as verified
        // so that we may not use this OTP by any accident
        $recent_user_otp->verified = true;
        $recent_user_otp->expired_at = Carbon::now();
        $recent_user_otp->save();

        // and in the end I make user verified in users table
        $user->email_verified_at = Carbon::now();
        $user->save();

        return response()->json([
            'message' => 'Your email has been verified successfully.'
        ]);

    }
}
