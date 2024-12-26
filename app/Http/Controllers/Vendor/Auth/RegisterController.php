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

    public function add(VendorAddRequest $request): JsonResponse
    {
        $recaptcha = getWebConfig('recaptcha');
        if (isset($recaptcha) && $recaptcha['status'] == 1) {
            try {
                $request->validate([
                    'g-recaptcha-response' => [
                        function ($attribute, $value, $fail) {
                            $secret_key = getWebConfig('recaptcha')['secret_key'];
                            $response = $value;
                            $url = 'https://www.google.com/recaptcha/api/siteverify?secret=' . $secret_key . '&response=' . $response;
                            $response = \file_get_contents($url);
                            $response = json_decode($response);
                            if (!$response->success) {
                                $fail(translate('ReCAPTCHA_Failed'));
                            }
                        },
                    ],
                ]);
            } catch (\Exception $exception) {
                return response()->json(['error' => translate('Captcha_Failed')]);
            }
        }

        // Assuming $vendor is obtained from the request data
        $vendorData = $request->validated();
        $vendorPhone = $vendorData['phone'] ?? null;

        if (!$vendorPhone) {
            return response()->json(['error' => translate('Phone_number_is_required')], 422);
        }

        // Generate OTP and save it
        $otp = new Otp();
        $otp->code = rand(100000, 999999);
        $otp->expiry = now()->addMinutes(15);
        $otp->phone = $vendorPhone;
        $otp->save();

        // Send OTP via AlphaSmsService
        try {
            $this->smsService->sendSms($vendorPhone, 'Your verification code is ' . $otp->code);
        } catch (\Exception $e) {
            return response()->json(['error' => translate('Failed_to_send_OTP')], 500);
        }

        return response()->json([
            'redirectRoute' => route('vendor.auth.registration.otp-verify'),
            'message' => translate('OTP_sent_to_phone'),
        ]);
    }

    public function verifyVendorOtp()
    {
        return view(Auth::VENDOR_VERIFY[VIEW]);
    }


    public function verifyOtp(Request $request): JsonResponse
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        // Retrieve OTP and data from the session
        $sessionOtp = Session::get('vendor_otp');
        $registrationData = Session::get('vendor_registration_data');

        if (!$sessionOtp || !$registrationData) {
            return response()->json(['error' => translate('Session_expired_or_invalid')]);
        }

        if ($request->otp != $sessionOtp) {
            return response()->json(['error' => translate('Invalid_OTP')]);
        }

        // Finalize vendor registration
        $vendor = $this->vendorRepo->add(data: $registrationData);
        $this->shopRepo->add($this->shopService->getAddShopDataForRegistration(request: (object)$registrationData, vendorId: $vendor['id']));
        $this->vendorWalletRepo->add($this->vendorService->getInitialWalletData(vendorId: $vendor['id']));

        $data = [
            'name' => $registrationData['f_name'],
            'status' => 'pending',
            'subject' => translate('Vendor_Registration_Successfully_Completed'),
            'title' => translate('registration_Complete') . '!',
            'message' => translate('congratulation') . '!' . translate('Your_registration_request_has_been_send_to_admin_successfully') . '!' . translate('Please_wait_until_admin_reviewal') . '.',
        ];
        event(new VendorRegistrationMailEvent($registrationData['email'], $data));

        // Clear session data
        Session::forget(['vendor_registration_data', 'vendor_otp']);

        return response()->json([
            'redirectRoute' => route('vendor.auth.login'),
            'message' => translate('Registration_successful'),
        ]);
    }
}
