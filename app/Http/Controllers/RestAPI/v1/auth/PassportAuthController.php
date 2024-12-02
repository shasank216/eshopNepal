<?php

namespace App\Http\Controllers\RestAPI\v1\auth;

use App\Http\Controllers\Controller;
use App\User;
use App\Models\Otp;
use App\Utils\CartManager;
use App\Utils\Helpers;
use App\Utils\SMS_module;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\AlphaSmsService;
use Illuminate\Support\Str;

class PassportAuthController extends Controller
{
    private $smsService;
    public function __construct(AlphaSmsService $smsService)
    {
        $this->smsService = $smsService;
        // $this->middleware('guest:customer', ['except' => ['logout']]);
    }
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // 'f_name' => 'required',
            // 'l_name' => 'required',
            // 'email' => 'required|unique:users',
            'phone' => 'required|unique:users',
            'password' => 'required|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        if ($request->referral_code) {
            $refer_user = User::where(['referral_code' => $request->referral_code])->first();
        }

        $temporary_token = Str::random(40);
        $user = User::create([
            'f_name' => $request->f_name,
            'l_name' => $request->l_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'gender' => $request->gender,
            'date_of_birth' => $request->date_of_birth,
            'is_active' => 1,
            'password' => bcrypt($request->password),
            'temporary_token' => $temporary_token,
            'referral_code' => Helpers::generate_referer_code(),
            'referred_by' => (isset($refer_user) && $refer_user) ? $refer_user->id : null,
        ]);

        // $phone_verification = Helpers::get_business_settings('phone_verification');
        // $email_verification = Helpers::get_business_settings('email_verification');
        // if ($phone_verification && !$user->is_phone_verified) {
        //     return response()->json(['temporary_token' => $temporary_token], 200);
        // }
        // if ($email_verification && !$user->is_email_verified) {
        //     return response()->json(['temporary_token' => $temporary_token], 200);
        // }

        // $token = $user->createToken('LaravelAuthApp')->accessToken;
        // return response()->json(['token' => $token], 200);

        // Generate and save OTP
        $otp = new Otp();
        $otp->code = rand(100000, 999999);
        $otp->expiry = Carbon::now()->addMinutes(15);
        $otp->phone = $user->phone;
        $otp->save();

        // Send OTP via SMS
        $this->smsService->sendSms($user->phone, 'Your verification code is ' . $otp->code);

        return response()->json([
            'status' => 'pending_verification',
            'message' => 'OTP sent to your phone.',
            'temporary_token' => $user->temporary_token,
        ], 200);
    }

    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required',
            'otp' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $otpRecord = Otp::where('phone', $request->phone)
            ->where('code', $request->otp)
            ->where('expiry', '>', Carbon::now())
            ->first();

        if (!$otpRecord) {
            return response()->json(['message' => 'Invalid or expired OTP'], 403);
        }

        // Verify phone and activate user
        $user = User::where('phone', $request->phone)->first();
        if ($user) {
            $user->is_active = 1;
            $user->is_phone_verified = 1;
            $user->phone_verified_at = now();
            $user->save();

            $otpRecord->delete(); // Delete OTP record after successful verification

            $token = $user->createToken('LaravelAuthApp')->accessToken;

            return response()->json(['token' => $token, 'message' => 'Phone verified successfully.'], 200);
        }

        return response()->json(['message' => 'User not found'], 404);
    }

    public function completeRegistration(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required',
            'email' => 'required|unique:users',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        // Find user by phone
        $user = User::where('phone', $request->phone)->first();
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Update user details
        $user->email = $request->email;
        $user->gender = $request->gender;
        $user->date_of_birth = $request->date_of_birth;
        $user->is_active = 1;
        $user->is_phone_verified = 1;  // Mark as phone verified
        $user->save();

        // Generate token
        $token = $user->createToken('LaravelAuthApp')->accessToken;

        return response()->json(['token' => $token, 'message' => 'Registration completed successfully.'], 200);
    }



    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required',
            'password' => 'required|min:6',
            'guest_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $user_id = $request->phone;
        $medium = 'phone'; // Assuming login is via phone

        // Check if the user exists with the provided phone
        $user = User::where($medium, $user_id)->first();

        if (!$user) {
            return response()->json([
                'errors' => [['code' => 'auth-001', 'message' => translate('credentials_doesnt_match')]],
            ], 401);
        }

        $max_login_hit = Helpers::get_business_settings('maximum_login_hit') ?? 5;
        $temp_block_time = Helpers::get_business_settings('temporary_login_block_time') ?? 5; // minutes

        // Handle temporary blocking logic
        if (isset($user->temp_block_time) && Carbon::parse($user->temp_block_time)->diffInSeconds() <= $temp_block_time) {
            $time = $temp_block_time - Carbon::parse($user->temp_block_time)->diffInSeconds();

            return response()->json([
                'errors' => [['code' => 'auth-001', 'message' => translate('please_try_again_after') . ' ' . CarbonInterval::seconds($time)->cascade()->forHumans()]],
            ], 401);
        }

        if (!$user->is_active) {
            return response()->json([
                'errors' => [['code' => 'auth-001', 'message' => translate('your_account_is_suspended')]],
            ], 401);
        }

        // Authentication attempt
        if (auth()->attempt([$medium => $user_id, 'password' => $request->password])) {
            $token = auth()->user()->createToken('LaravelAuthApp')->accessToken;

            // Reset login attempt counters
            $user->login_hit_count = 0;
            $user->is_temp_blocked = 0;
            $user->temp_block_time = null;
            $user->updated_at = now();
            $user->save();

            CartManager::cart_to_db($request);

            return response()->json(['token' => $token], 200);
        } else {
            // Handle failed login attempt
            $user->login_hit_count += 1;

            if ($user->login_hit_count >= $max_login_hit) {
                $user->is_temp_blocked = 1;
                $user->temp_block_time = now();
            }

            $user->save();

            return response()->json([
                'errors' => [['code' => 'auth-001', 'message' => translate('credentials_doesnt_match')]],
            ], 401);
        }
    }


    public function logout(Request $request)
    {
        if (auth()->check()) {
            auth()->user()->token()->revoke();
            return response()->json(['message' => translate('logged_out_successfully')], 200);
        }
        return response()->json(['message' => translate('logged_out_fail')], 403);
    }
}
