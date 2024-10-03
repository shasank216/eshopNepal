<?php

namespace App\Services;

use App\Traits\FileManagerTrait;
use Illuminate\Support\Str;

class VendorService
{
    use FileManagerTrait;
    /**
     * @param string $email
     * @param string $password
     * @param string|bool|null $rememberToken
     * @return bool
     */
    public function isLoginSuccessful(string $email, string $password, string|null|bool $rememberToken): bool
    {
        if (auth('seller')->attempt(['email' => $email, 'password' => $password], $rememberToken)) {
            return true;
        }
        return false;
    }

    /**
     * @return array
     */
    public function getInitialWalletData(int $vendorId): array
    {
        return [
            'seller_id' => $vendorId,
            'withdrawn' => 0,
            'commission_given' => 0,
            'total_earning' => 0,
            'pending_withdraw' => 0,
            'delivery_charge_earned' => 0,
            'collected_cash' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function logout(): void
    {
        auth()->guard('seller')->logout();
        session()->invalidate();
    }

    /**
     * @param object $request
     * @return array
     */
    public function getFreeDeliveryOverAmountData(object $request):array
    {
        return [
            'free_delivery_status' => $request['free_delivery_status'] == 'on' ? 1 : 0,
            'free_delivery_over_amount' => currencyConverter($request['free_delivery_over_amount'], 'usd'),
        ];
    }

    /**
     * @return array[minimum_order_amount: float|int]
     */
    public function getMinimumOrderAmount(object $request) :array
    {
        return [
            'minimum_order_amount' => currencyConverter($request['minimum_order_amount'], 'usd')
        ];
    }

    /**
     * @param object $request
     * @param object $vendor
     * @return array
     */
    public function getVendorDataForUpdate(object $request, object $vendor):array
    {
        $image = $request['image'] ? $this->update(dir: 'seller/', oldImage: $vendor['image'], format: 'webp', image: $request->file('image')) : $vendor['image'];
        $vat_pan_img = $request['vat_pan_img'] ? $this->update(dir: 'vat_pan_img/', oldImage: $vendor['vat_pan_img'], format:
        'webp', image: $request->file('vat_pan_img')) : $vendor['vat_pan_img'];
        $registration_cert_img = $request['registration_cert_img'] ? $this->update(dir: 'registration_cert_img/', oldImage: $vendor['registration_cert_img'], format:'webp', image: $request->file('registration_cert_img')) : $vendor['registration_cert_img'];
        $citizenship_img = $request['citizenship_img'] ? $this->update(dir: 'citizenship_img/', oldImage: $vendor['citizenship_img'], format:'webp', image: $request->file('citizenship_img')) : $vendor['citizenship_img'];

        return [
            'f_name' => $request['f_name'],
            'l_name' => $request['l_name'],
            'phone' => $request['phone'],
            'image' => $image,
            'vat_pan_img' => $vat_pan_img,
            'registration_cert_img' => $registration_cert_img,
            'citizenship_img' => $citizenship_img
        ];
    }


    public function getVendorPasswordData(object $request):array
    {
        return [
            'password' => bcrypt($request['password']),
        ];
    }

    public function getVendorBankInfoData(object $request):array
    {
        return [
            'bank_name' => $request['bank_name'],
            'branch' => $request['branch'],
            'holder_name' => $request['holder_name'],
            'account_no' => $request['account_no'],
        ];
    }
    public function getAddData(object $request):array
    {
        return [
            'f_name' => $request['f_name'],
            'l_name' => $request['l_name'],
            'phone' => $request['phone'],
            'email' => $request['email'],
            'address' => $request['address'],
            'image' => $this->upload(dir: 'seller/', format: 'webp', image: $request->file('image')),
            'vat_pan_img' => $this->upload(dir: 'vat_pan_img/', format: 'webp', image: $request->file('vat_pan_img')),
            'registration_cert_img' => $this->upload(dir: 'registration_cert_img/', format: 'webp', image:
            $request->file('registration_cert_img')),
            'citizenship_img' => $this->upload(dir: 'citizenship_img/', format: 'webp', image:
            $request->file('citizenship_img')),

            'password' => bcrypt($request['password']),
            'status' => $request['status'] == 'approved' ? 'approved' : 'pending',
        ];
    }
}
