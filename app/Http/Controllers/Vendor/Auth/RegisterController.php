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

    public function add(VendorAddRequest $request)
    {
        // Extract vendor data but exclude files
        $vendorData = $request->except(['image', 'vat_pan_img', 'company_cheque_book', 'logo', 'banner']);  // Exclude file fields

        // If vendorData is an array, convert it to an object
        $vendorData = (object) $vendorData;

        // Handle the files separately and store their paths
        $filePaths = [];

        // Handle image file upload
        if ($request->hasFile('image')) {
            $filePaths['image'] = $request->file('image')->store('vendor_images');
        }

        // Handle vat_pan_img file upload
        if ($request->hasFile('vat_pan_img')) {
            $filePaths['vat_pan_img'] = $request->file('vat_pan_img')->store('vendor_vat_images');
        }

        // Handle company_cheque_book file upload
        if ($request->hasFile('company_cheque_book')) {
            $filePaths['company_cheque_book'] = $request->file('company_cheque_book')->store('vendor_cheque_images');
        }

        // Handle logo file upload
        if ($request->hasFile('logo')) {
            $filePaths['logo'] = $request->file('logo')->store('vendor_logos');
        }

        // Handle banner file upload
        if ($request->hasFile('banner')) {
            $filePaths['banner'] = $request->file('banner')->store('vendor_banners');
        }

        // Merge file paths with vendor data
        $vendorData = (object) array_merge((array) $vendorData, $filePaths);

        // Generate OTP
        $otpCode = rand(100000, 999999);
        try {
            $otp = Otp::create([
                'phone' => $vendorData->phone,  // Access using -> since it's now an object
                'code' => $otpCode,
                'expiry' => now()->addMinutes(15),
            ]);

            $this->smsService->sendSms($vendorData->phone, 'Your verification code is ' . $otpCode);

            // Store sanitized data in session
            Session::put('vendor_registration_data', $vendorData);

            session()->flash('success', translate('OTP sent to phone'));
            return redirect()->route('vendor.auth.registration.verifyVendorOtp');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['otp' => translate('Failed to send OTP')]);
        }
    }


    public function verifyVendorOtp()
    {
        return view(Auth::VENDOR_VERIFY[VIEW]);
    }


    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        $vendorData = Session::get('vendor_registration_data');
        if (!$vendorData) {
            session()->flash('error', translate('Session expired or invalid'));
            return redirect()->route('vendor.auth.registration.index');
        }

        $otpRecord = Otp::where('phone', $vendorData['phone'])
            ->where('code', $request->otp)
            ->where('expiry', '>', now())
            ->first();
        // dd($otpRecord);
        if (!$otpRecord) {
            session()->flash('error', translate('Invalid or expired OTP'));
            return redirect()->back();
        }

        try {
            $vendor = $this->vendorRepo->add($vendorData);
            $this->shopRepo->add($this->shopService->getAddShopDataForRegistration((object)$vendorData, $vendor->id));
            $this->vendorWalletRepo->add($this->vendorService->getInitialWalletData($vendor->id));

            $otpRecord->delete();
            Session::forget('vendor_registration_data');

            // Show success message only after successful OTP verification
            session()->flash('success', translate('Registration successful'));
            return redirect()->route('vendor.auth.login');
        } catch (\Exception $e) {
            session()->flash('error', translate('Failed to complete registration'));
            return redirect()->route('vendor.auth.registration.index');
        }
    }
}
