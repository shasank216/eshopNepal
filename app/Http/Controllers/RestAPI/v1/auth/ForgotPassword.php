<?php

namespace App\Http\Controllers\RestAPI\v1\auth;

use App\Events\PasswordResetMailEvent;
use App\Http\Controllers\Controller;
use App\Models\PasswordReset;
use App\Models\Otp;
use App\User;
use App\Utils\Helpers;
use App\Utils\SMS_module;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use App\Services\AlphaSmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Modules\Gateways\Traits\SmsGateway;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;



class ForgotPassword extends Controller
{
    private $smsService;
    public function __construct(AlphaSmsService $smsService)
    {
        $this->smsService = $smsService;
        // $this->middleware('guest:customer', ['except' => ['logout']]);
    }
    public function reset_password_request(Request $request)
    {
        // dd('hii');
        $request->validate([
            'identity' => 'required',
            'recover_type' => 'required|in:email,phone',
        ]);

        if ($request->recover_type === 'phone') {
            $user = User::where('phone', $request->identity)
                ->orWhere('phone', '+977' . $request->identity)
                ->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please enter a valid phone number.',
                ], 404);
            }

            $otp = new Otp();
            $otp->phone = $request->identity;
            $otp->code = rand(100000, 999999);
            $otp->save();

            $this->smsService->sendSms($request->identity, 'Your verification code is ' . $otp->code);
            return response()->json([
                'success' => true,
                'message' => 'OTP has been sent to your phone number.',
            ], 200);
        }

        $verificationBy = Helpers::get_business_settings('forgot_password_verification');
        $otpIntervalTime = Helpers::get_business_settings('otp_resend_time') ?? 1;

        if ($verificationBy === 'email') {
            $customer = User::where('email', $request->identity)->first();

            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'No user found with this email address.',
                ], 404);
            }

            $passwordReset = PasswordReset::where('identity', $customer->email)->latest()->first();

            if ($passwordReset && Carbon::parse($passwordReset->created_at)->diffInSeconds() < $otpIntervalTime * 60) {
                $timeRemaining = $otpIntervalTime * 60 - Carbon::parse($passwordReset->created_at)->diffInSeconds();

                return response()->json([
                    'success' => false,
                    'message' => 'Please try again after ' . gmdate("i:s", $timeRemaining) . ' minutes.',
                ], 429);
            }

            $token = Str::random(120);
            $passwordReset = PasswordReset::updateOrCreate(
                ['identity' => $customer->email],
                ['token' => $token, 'user_type' => 'customer', 'created_at' => now()]
            );

            $resetUrl = url('/') . '/customer/auth/reset-password?token=' . $token;
            PasswordResetMailEvent::dispatch($customer->email, $resetUrl);

            return response()->json([
                'success' => true,
                'message' => 'Password reset link sent to your email.',
            ], 200);
        }

        if ($verificationBy === 'phone') {
            $customer = User::where('phone', 'like', "%{$request->identity}%")->first();

            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'No user found with this phone number.',
                ], 404);
            }

            $passwordReset = PasswordReset::where('identity', $customer->phone)->latest()->first();

            if ($passwordReset && Carbon::parse($passwordReset->created_at)->diffInSeconds() < $otpIntervalTime * 60) {
                $timeRemaining = $otpIntervalTime * 60 - Carbon::parse($passwordReset->created_at)->diffInSeconds();

                return response()->json([
                    'success' => false,
                    'message' => 'Please try again after ' . gmdate("i:s", $timeRemaining) . ' minutes.',
                ], 429);
            }

            $token = rand(1000, 9999);
            $passwordReset = PasswordReset::updateOrCreate(
                ['identity' => $customer->phone],
                ['token' => $token, 'user_type' => 'customer', 'created_at' => now()]
            );

            $response = SmsGateway::send($customer->phone, $token);

            if ($response === "not_found" || $response !== "success") {
                return response()->json([
                    'success' => false,
                    'message' => 'SMS configuration missing.',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'OTP has been sent to your phone number.',
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
            'identity' => 'required|regex:/^\+?[1-9]\d{1,14}$/', // Validate phone number (international format)
            'otp' => 'required|numeric|digits:6', // Ensure the OTP is numeric and 6 digits
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
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'identity' => 'required|regex:/^\+?[1-9]\d{1,14}$/', // Validates phone number (international format)
            'otp' => 'required|numeric|digits:6', // Validates OTP (6 digits)
            'password' => 'required|same:confirm_password|min:8', // Validates password and confirm_password
        ]);

        if ($validator->fails()) {
            // Return the validation errors as a response
            return response()->json([
                'errors' => $validator->errors()->all()  // Fetch all error messages as an array
            ], 403);
        }

        // Verify OTP from the database
        $otpExists = Otp::where('phone', $request->identity)
            ->where('code', $request->otp)
            ->orWhere('phone', '+977' . $request->identity)
            ->first();
        if (!$otpExists) {
            return response()->json(['message' => 'Invalid OTP'], 400);
        }

        // Mark OTP as used
        $otpExists->used = 1;
        $otpExists->save();

        // Find the user by phone number
        $user = User::where('phone', $request->identity)
            ->orWhere('phone', $request->identity)
            ->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Hash the new password
        $newPasswordHash = Hash::make($request->password);

        // Directly update the password in the database
        $updated = User::where('id', $user->id)->update(['password' => $newPasswordHash]);

        if (!$updated) {
            return response()->json(['message' => 'Failed to update password'], 500);
        }

        return response()->json(['message' => 'Password reset successfully'], 200);
    }
}
