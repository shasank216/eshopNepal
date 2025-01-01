<?php

namespace App\Http\Controllers\Vendor\Auth;

use App\Contracts\Repositories\PasswordResetRepositoryInterface;
use App\Contracts\Repositories\VendorRepositoryInterface;
use App\Enums\SessionKey;
use App\Enums\ViewPaths\Vendor\Auth;
use App\Enums\ViewPaths\Vendor\ForgotPassword;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Vendor\PasswordResetRequest;
use App\Http\Requests\Vendor\VendorPasswordRequest;
use App\Services\PasswordResetService;
use App\Traits\SmsGateway;
use App\Models\Seller;
use App\Utils\Helpers;
use App\Utils\SMS_module;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Services\AlphaSmsService;
use Modules\Gateways\Traits\SmsGateway as AddonSmsGateway;

class ForgotPasswordController extends BaseController
{
    use SmsGateway;

    /**
     * @param VendorRepositoryInterface $vendorRepo
     * @param PasswordResetRepositoryInterface $passwordResetRepo
     * @param PasswordResetService $passwordResetService
     */
    public function __construct(
        private readonly VendorRepositoryInterface $vendorRepo,
        private readonly PasswordResetRepositoryInterface $passwordResetRepo,
        private readonly PasswordResetService $passwordResetService,
        private readonly AlphaSmsService $smsService
    ) {
        $this->middleware('guest:seller', ['except' => ['logout']]);
    }

    /**
     * @param Request|null $request
     * @param string|null $type
     * @return View|Collection|LengthAwarePaginator|callable|RedirectResponse|null
     */
    public function index(?Request $request, string $type = null): View|Collection|LengthAwarePaginator|null|callable|RedirectResponse
    {
        return $this->getForgotPasswordView();
    }

    /**
     * @return View
     */
    public function getForgotPasswordView(): View
    {
        return view(ForgotPassword::INDEX[VIEW]);
    }

    /**
     * @param PasswordResetRequest $request
     * @return JsonResponse
     */
    public function getPasswordResetRequest(PasswordResetRequest $request): JsonResponse
    {
        $identity = $request['identity'];
        session()->put(SessionKey::FORGOT_PASSWORD_IDENTIFY, $identity);

        // Check if the user exists
        $vendor = Seller::whereRaw("REPLACE(phone, '+', '') = ?", [str_replace('+', '', $identity)])
            ->orWhere('email', $identity)
            ->first();
        // dd($vendor);
        if (!$vendor) {
            return response()->json(['error' => translate('No user found with this identity') . '!!']);
        }

        // Generate and store OTP
        $otpCode = rand(100000, 999999);
        DB::table('otps')->insert([
            'phone' => $vendor['phone'],
            'code' => $otpCode,
            'expiry' => now()->addMinutes(15),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Send OTP via Alpha SMS
        try {
            $this->smsService->sendSms($vendor['phone'], 'Your OTP for password reset is: ' . $otpCode);
        } catch (\Exception $e) {
            return response()->json(['error' => translate('SMS sending failed') . '!!']);
        }

        return response()->json([
            'verificationBy' => 'phone',
            'redirectRoute' => route('vendor.auth.forgot-password.otp-verification'),
            'success' => translate('Check your phone for the OTP'),
        ]);
    }

    /**
     * @return View
     */
    public function getOTPVerificationView(): View
    {
        return view(ForgotPassword::OTP_VERIFICATION[VIEW]);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function submitOTPVerificationCode(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'otp' => 'required|digits:6',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $identity = preg_replace('/\D/', '', session(SessionKey::FORGOT_PASSWORD_IDENTIFY));
        // dd($identity);
        $otpRecord = DB::table('otps')
            ->where('code', $request['otp'])
            ->where('phone', $identity)
            ->where('expiry', '>', now())
            ->first();
        //  dd($otpRecord);

        if (!$otpRecord) {
            // Use Toastr to display an error message on the frontend
            Toastr::error(translate('Invalid OTP') . '!!');
            return redirect()->back(); // Redirect back to the form
        }

        // Delete OTP after successful verification
        DB::table('otps')->where('id', $otpRecord->id)->delete();

        // Redirect to reset password view with token
        // return redirect()->route('vendor.auth.forgot-password.reset-password', ['identity' => $identity]);
        return redirect()->route('vendor.auth.forgot-password.reset-password', ['identity' => $identity])
                 ->with('success', 'OTP verified successfully!');

    }
    /**
     * @param Request $request
     * @return View|RedirectResponse
     */
    public function getPasswordResetView(Request $request): View|RedirectResponse
    {
        $passwordResetData = $this->passwordResetRepo->getFirstWhere(params: ['user_type' => 'seller', 'token' => $request['token']]);
        if (isset($passwordResetData)) {
            $token = $request['token'];
            return view(ForgotPassword::RESET_PASSWORD[VIEW], compact('token'));
        }
        Toastr::error(translate('Invalid_URL'));
        return redirect()->route(Auth::VENDOR_LOGOUT[URI]);
    }
    /**
     * @param VendorPasswordRequest $request
     * @return JsonResponse
     */
    public function resetPassword(VendorPasswordRequest $request): JsonResponse
    {
        $passwordResetData = $this->passwordResetRepo->getFirstWhere(params: ['user_type' => 'seller', 'token' => $request['reset_token']]);
        if ($passwordResetData) {
            $vendor = $this->vendorRepo->getFirstWhere(params: ['identity' => $passwordResetData['identity']]);
            $this->vendorRepo->update(id: $vendor['id'], data: ['password' => bcrypt($request['password'])]);
            $this->passwordResetRepo->delete(params: ['id' => $passwordResetData['id']]);
            return response()->json([
                'passwordUpdate' => 1,
                'success' => translate('Password_reset_successfully'),
                'redirectRoute' => route(Auth::VENDOR_LOGOUT[URI])
            ]);
        } else {
            return response()->json(['error' => translate('invalid_URL')]);
        }
    }
}
