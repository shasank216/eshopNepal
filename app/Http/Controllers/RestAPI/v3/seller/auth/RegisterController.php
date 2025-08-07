<?php

namespace App\Http\Controllers\RestAPI\v3\seller\auth;

use App\Events\VendorRegistrationMailEvent;
use App\Http\Controllers\Controller;
use App\Models\Seller;
use App\Models\Shop;
use App\Utils\Helpers;
use App\Utils\ImageManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Services\AlphaSmsService;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    private $smsService;
    public function __construct(AlphaSmsService $smsService)
    {
        $this->smsService = $smsService;
        // $this->middleware('guest:customer', ['except' => ['logout']]);
    }
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email'         => 'required|unique:sellers',
            'shop_address'  => 'required',
            'f_name'        => 'required',
            'l_name'        => 'required',
            'shop_name'     => 'required',
            'phone'         => 'required|unique:sellers',
            'password'      => 'required|min:8',
            'image'         => 'required|mimes: jpg,jpeg,png,gif',
            'logo'          => 'required|mimes: jpg,jpeg,png,gif',
            'banner'        => 'required|mimes: jpg,jpeg,png,gif',
            'bottom_banner' => 'mimes: jpg,jpeg,png,gif',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => Helpers::error_processor($validator)], 403);
        }

        DB::beginTransaction();
        try {
            $seller = new Seller();
            $seller->f_name = $request->f_name;
            $seller->l_name = $request->l_name;
            $seller->phone = $request->phone;
            $seller->email = $request->email;
            $seller->image = ImageManager::upload('seller/', 'webp', $request->file('image'));
            $seller->password = bcrypt($request->password);
            $seller->status =  $request->status == 'approved' ? 'approved' : "pending";
            $seller->save();

            $shop = new Shop();
            $shop->seller_id = $seller->id;
            $shop->name = $request->shop_name;
            $shop->address = $request->shop_address;
            $shop->contact = $request->phone;
            $shop->image = ImageManager::upload('shop/', 'webp', $request->file('logo'));
            $shop->banner = ImageManager::upload('shop/banner/', 'webp', $request->file('banner'));
            $shop->bottom_banner = ImageManager::upload('shop/banner/', 'webp', $request->file('bottom_banner'));
            $shop->save();

            DB::table('seller_wallets')->insert([
                'seller_id' => $seller['id'],
                'withdrawn' => 0,
                'commission_given' => 0,
                'total_earning' => 0,
                'pending_withdraw' => 0,
                'delivery_charge_earned' => 0,
                'collected_cash' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            DB::commit();
            $data = [
                'name' => $request['f_name'],
                'status' => 'pending',
                'subject' => translate('Vendor_Registration_Successfully_Completed'),
                'title' => translate('registration_Complete') . '!',
                'message' => translate('congratulation') . '!' . translate('Your_registration_request_has_been_send_to_admin_successfully') . '!' . translate('Please_wait_until_admin_reviewal') . '.',
            ];
            event(new VendorRegistrationMailEvent($request['email'], $data));
            // Generate OTP
            $otpCode = rand(100000, 999999);
            DB::table('otps')->insert([
                'phone' => $request->phone,
                'code' => $otpCode,
                'expiry' => now()->addMinutes(15),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Send OTP via Alpha SMS
            $this->smsService->sendSms($request->phone, 'Your verification code is: ' . $otpCode);

            DB::commit();

            return response()->json([
                'status' => 'pending_verification',
                'message' => 'OTP sent to your phone for verification.',
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Shop registration failed!', 'error' => $e->getMessage()], 500);
        }
    }

    public function verifyOtp(Request $request): JsonResponse
{
    $validator = Validator::make($request->all(), [
        'phone' => 'required|regex:/^\+?[0-9]{10,15}$/',
        'otp' => 'required|digits:6',
    ]);

    if ($validator->fails()) {
        return response()->json(['message' => Helpers::error_processor($validator)], 403);
    }

    $otpRecord = DB::table('otps')
        ->where('phone', $request->phone)
        ->where('code', $request->otp)
        ->where('expiry', '>', now())
        ->first();

    if ($otpRecord) {
        // Mark phone as verified
        Seller::where('phone', $request->phone)->update(['phone_verified_at' => now()]);
        DB::table('otps')->where('id', $otpRecord->id)->delete();

        return response()->json(['message' => 'Phone number verified successfully.'], 200);
    } else {
        return response()->json(['message' => 'Invalid or expired OTP.'], 403);
    }
}

}
