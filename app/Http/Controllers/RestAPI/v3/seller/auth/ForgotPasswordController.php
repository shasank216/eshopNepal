<?php

namespace App\Http\Controllers\RestAPI\v3\seller\auth;

use App\Events\PasswordResetMailEvent;
use App\Http\Controllers\Controller;
use App\Models\Seller;
use App\Models\PasswordReset;
use App\Utils\Helpers;
use App\Utils\SMS_module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Modules\Gateways\Traits\SmsGateway;
use App\Services\AlphaSmsService;
use App\Models\Otp;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;


class ForgotPasswordController extends Controller
{
    private $smsService;

    public function __construct(AlphaSmsService $smsService)
    {
        $this->smsService = $smsService;
    }
    public function reset_password_request(Request $request)
    {
        $request->validate([
            'identity' => 'required',
            'recover_type' => 'required|in:email,phone',
        ]);

        $otpIntervalTime = Helpers::get_business_settings('otp_resend_time') ?? 1;

        if ($request->recover_type === 'phone') {
            $seller = Seller::where('phone', $request->identity)->first();

            if (!$seller) {
                return response()->json([
                    'success' => false,
                    'message' => 'No seller found with this phone number.',
                ], 404);
            }

            $passwordReset = PasswordReset::where('identity', $seller->phone)->latest()->first();

            if ($passwordReset && Carbon::parse($passwordReset->created_at)->diffInSeconds() < $otpIntervalTime * 60) {
                $timeRemaining = $otpIntervalTime * 60 - Carbon::parse($passwordReset->created_at)->diffInSeconds();

                return response()->json([
                    'success' => false,
                    'message' => 'Please try again after ' . gmdate("i:s", $timeRemaining) . ' minutes.',
                ], 429);
            }

            $token = rand(1000, 9999);
            $passwordReset = PasswordReset::updateOrCreate(
                ['identity' => $seller->phone],
                ['token' => $token, 'user_type' => 'seller', 'created_at' => now()]
            );

            $otp = new Otp();
            $otp->phone = $request->identity;
            $otp->code = rand(100000, 999999);
            $otp->save();

            $this->smsService->sendSms($request->identity, 'Your verification code is ' . $otp->code);

            // Log::info('SMS Service Response: ' . $response); // Log the response
            return response()->json([
                'success' => true,
                'message' => 'OTP has been sent to your phone number.',
            ], 200);
        }

        if ($request->recover_type === 'email') {
            $seller = Seller::where('email', $request->identity)->first();

            if (!$seller) {
                return response()->json([
                    'success' => false,
                    'message' => 'No seller found with this email address.',
                ], 404);
            }

            $passwordReset = PasswordReset::where('identity', $seller->email)->latest()->first();

            if ($passwordReset && Carbon::parse($passwordReset->created_at)->diffInSeconds() < $otpIntervalTime * 60) {
                $timeRemaining = $otpIntervalTime * 60 - Carbon::parse($passwordReset->created_at)->diffInSeconds();

                return response()->json([
                    'success' => false,
                    'message' => 'Please try again after ' . gmdate("i:s", $timeRemaining) . ' minutes.',
                ], 429);
            }

            $token = Str::random(120);
            $passwordReset = PasswordReset::updateOrCreate(
                ['identity' => $seller->email],
                ['token' => $token, 'user_type' => 'seller', 'created_at' => now()]
            );

            $resetUrl = url('/') . '/seller/auth/reset-password?token=' . $token;
            PasswordResetMailEvent::dispatch($seller->email, $resetUrl);

            return response()->json([
                'success' => true,
                'message' => 'Password reset link sent to your email.',
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid recover type.',
        ], 400);
    }


    public function otp_verification_submit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'identity' => 'required',
            'otp' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $identity = $request->input('identity'); // This is the phone number
        $otp = $request->input('otp');

        // Fetch the OTP from the Otp table
        $otp_record = Otp::where('phone', $identity)
            ->where('code', $otp)
            ->first();
        // If no record exists for the given phone number and OTP, return an error
        if (!$otp_record) {
            return response()->json([
                'message' => translate('invalid_otp')
            ], 403);
        }

        // Check if OTP is still valid by verifying its expiration time
        $otp_expiration_time = Carbon::parse($otp_record->created_at)->addMinutes(10); // Adjust expiration time as needed
        if (Carbon::now()->greaterThan($otp_expiration_time)) {
            return response()->json([
                'message' => translate('otp_expired')
            ], 403);
        }

        // If OTP is correct and within expiration time, proceed with verification
        return response()->json([
            'message' => translate('otp_verified')
        ], 200);
    }

    public function reset_password_submit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'identity' => 'required',
            'otp' => 'required',
            'password' => 'required|same:confirm_password|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $otpExists = Otp::where('phone', $request->identity)
            ->where('code', $request->otp)
            ->orWhere('phone' . $request->identity)
            ->first();
        if (!$otpExists) {
            return response()->json(['message' => 'Invalid OTP'], 400);
        }

        // Mark OTP as used
        $otpExists->used = 1;
        $otpExists->save();

        // Find the user by phone number
        $seller = Seller::where('phone', $request->identity)
            ->orWhere('phone' . $request->identity)
            ->first();
        if (!$seller) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Reset the password for the user
        $seller->password = Hash::make($request->password);
        $seller->save();

        // Optionally, delete OTP after password reset to prevent reuse
        $otpExists->delete();

        return response()->json(['message' => 'Password reset successfully'], 200);
    }
}
