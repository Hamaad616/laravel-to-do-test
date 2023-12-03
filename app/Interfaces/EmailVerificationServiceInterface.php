<?php

namespace App\Interfaces;

use App\Models\User;
use Illuminate\Http\JsonResponse;

interface EmailVerificationServiceInterface
{

    function generateEmail(User $user, $otp): JsonResponse;

    function generateOtp(User $user): JsonResponse;

    function verifyOtp(User $user, int $otp_digits): JsonResponse;

}
