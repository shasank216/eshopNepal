<?php

namespace App\Http\Controllers\Vendor\Auth;

use App\Contracts\Repositories\ShopRepositoryInterface;
use App\Contracts\Repositories\VendorRepositoryInterface;
use App\Contracts\Repositories\VendorWalletRepositoryInterface;
use App\Enums\SessionKey;
use App\Enums\ViewPaths\Vendor\Auth;
use App\Events\VendorRegistrationMailEvent;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Vendor\VendorAddRequest;
use App\Services\ShopService;
use App\Models\Category;
use App\Services\VendorService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\Otp;
use Illuminate\Pagination\LengthAwarePaginator;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Session;
use App\Traits\CommonTrait;
use Modules\Gateways\Traits\SmsGateway;
use App\Utils\SMS_module;
use Carbon\Carbon;
use App\Services\AlphaSmsService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\Seller;
use App\Models\VendorWallet;
use App\Models\Shop;
use App\Utils\ImageManager;
use Illuminate\Validation\Rules\Password;

class RegisterController extends BaseController
{
    // private $smsService;
    use CommonTrait;
    public function __construct(
        private readonly VendorRepositoryInterface $vendorRepo,
        private readonly VendorWalletRepositoryInterface $vendorWalletRepo,
        private readonly ShopRepositoryInterface $shopRepo,
        private readonly VendorService $vendorService,
        private readonly ShopService $shopService,
        private readonly AlphaSmsService $smsService
    ) {
        // $this->smsService = $smsService;
        // Additional initialization if needed
        // $this->middleware('guest:customer', ['except' => ['logout']]);
    }

    public function index(?Request $request, string $type = null): View|Collection|LengthAwarePaginator|null|callable|RedirectResponse
    {

        return $this->getView();
    }
    /*public function getView():View|RedirectResponse
    {

        $businessMode = getWebConfig(name:'business_mode');
        $vendorRegistration = getWebConfig(name:'seller_registration');
        if((isset($businessMode) && $businessMode=='single') || (isset($vendorRegistration) && $vendorRegistration==0))
        {
            Toastr::warning(translate('access_denied').'!!');
            return redirect('/');
        }

        $categories = Category::all();

        return view(VIEW_FILE_NAMES[Auth::VENDOR_REGISTRATION[VIEW]], [
            'categories' => $categories,
        ]);
    }*/

    public function getView(): View|RedirectResponse
    {
        // dd("hello");
        $businessMode = getWebConfig(name: 'business_mode');
        $vendorRegistration = getWebConfig(name: 'seller_registration');

        // Check if registration is allowed
        if ((isset($businessMode) && $businessMode == 'single') || (isset($vendorRegistration) && $vendorRegistration == 0)) {
            Toastr::warning(translate('access_denied') . '!!');
            return redirect('/');
        }

        // Get categories
        $categories = Category::all();

        // Check if country restriction is enabled
        $country_restrict_status = getWebConfig(name: 'delivery_country_restriction');

        // Get countries based on restriction status
        $countries = $country_restrict_status ? $this->get_delivery_country_array() : COUNTRIES;

        // Initialize arrays for country names and codes
        $countriesName = [];
        $countriesCode = [];

        // Populate the arrays
        foreach ($countries as $country) {
            $countriesName[] = $country['name'];
            $countriesCode[] = $country['code'];
        }

        // Pass variables to the view
        return view(VIEW_FILE_NAMES[Auth::VENDOR_REGISTRATION[VIEW]], [
            'categories' => $categories,
            'countriesName' => $countriesName,
            'countriesCode' => $countriesCode,
            'country_restrict_status' => $country_restrict_status,
        ]);
    }

    public function add(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'         => 'required|unique:sellers',
            'shop_address'  => 'required',
            'f_name'        => 'required',
            'l_name'        => 'required',
            'shop_name'     => 'required',
            'phone'         => 'required|unique:sellers',
            'password'      => [
                'required',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ],
            'image'         => 'required|mimes:jpg,jpeg,png,gif',
            'logo'          => 'required|mimes:jpg,jpeg,png,gif',
            'banner'        => 'required|mimes:jpg,jpeg,png,gif',
            'bottom_banner' => 'mimes:jpg,jpeg,png,gif',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
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
            $seller->status = 'pending';
            $seller->save();

            $shop = new Shop();
            $shop->seller_id = $seller->id;
            $shop->name = $request->shop_name;
            $shop->address = $request->shop_address;
            $shop->longitude = $request->longitude;
            $shop->latitude = $request->latitude;
            $shop->contact = $request->phone;
            $shop->image = ImageManager::upload('shop/', 'webp', $request->file('logo'));
            $shop->banner = ImageManager::upload('shop/banner/', 'webp', $request->file('banner'));
            $shop->bottom_banner = ImageManager::upload('shop/banner/', 'webp', $request->file('bottom_banner'));
            $shop->save();

            DB::table('seller_wallets')->insert([
                'seller_id' => $seller->id,
                'withdrawn' => 0,
                'commission_given' => 0,
                'total_earning' => 0,
                'pending_withdraw' => 0,
                'delivery_charge_earned' => 0,
                'collected_cash' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $data = [
                'name' => $request->f_name,
                'status' => 'pending',
                'subject' => __('Vendor Registration Successfully Completed'),
                'title' => __('Registration Complete') . '!',
                'message' => __('Congratulations! Your registration request has been sent to the admin successfully. Please wait until the admin reviews it.'),
            ];
            event(new VendorRegistrationMailEvent($request->email, $data));

            $otpCode = rand(100000, 999999);
            DB::table('otps')->insert([
                'phone' => $request->phone,
                'code' => $otpCode,
                'expiry' => now()->addMinutes(15),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->smsService->sendSms($request->phone, 'Your verification code is: ' . $otpCode);

            DB::commit();
            return response()->json(
                [
                    'redirectRoute' => route('vendor-verify')
                ]
            );
            // return redirect()->route('vendor-verify')
            //     ->with('success', 'Registration successful! An OTP has been sent to your phone for verification.');
            // return view(VIEW_FILE_NAMES[Auth::VENDOR_VERIFY[VIEW]]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Shop registration failed! ' . $e->getMessage());
        }
    }


    public function getOTPVerificationView()
    {

        return view(VIEW_FILE_NAMES[Auth::VENDOR_VERIFY[VIEW]]);
        // return view('web-views.seller-view.auth.verify');
    }


    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'otp' => 'required|digits:6',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Retrieve the phone number using the OTP
        $otpRecord = DB::table('otps')
            ->where('code', $request->otp)
            ->where('expiry', '>', now())
            ->first();

        // if ($otpRecord) {
        //     // Update the seller's phone verification
        //     Seller::where('phone', $otpRecord->phone)->update(['phone_verified_at' => now()]);
        //     DB::table('otps')->where('id', $otpRecord->id)->delete();

        //     return response()->json(
        //         [
        //             'redirectRoute' => route('vendor.auth.login')
        //         ]
        //     )->with('success', 'Phone number verified successfully.');
        // }

        // if ($otpRecord) {
        //     // Update the seller's phone verification
        //     Seller::where('phone', $otpRecord->phone)->update(['phone_verified_at' => now()]);
        //     DB::table('otps')->where('id', $otpRecord->id)->delete();

        //     // Return a JSON response
        //     return response()->json(
        //         [
        //             'redirectRoute' => route('vendor.auth.login')
        //         ]
        //     );

        // }
        if ($otpRecord) {
            // Update the seller's phone verification
            Seller::where('phone', $otpRecord->phone)->update(['phone_verified_at' => now()]);
            DB::table('otps')->where('id', $otpRecord->id)->delete();

            // Redirect to vendor login page
            return redirect()->route('vendor.auth.login')->with('success', 'Phone number verified successfully.');
        }

        // Show an error message using Toastr
        Toastr::error(translate('invalid_otp'));
        return redirect()->back();
    }
}
