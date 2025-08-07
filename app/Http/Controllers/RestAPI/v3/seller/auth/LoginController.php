<?php

namespace App\Http\Controllers\RestAPI\v3\seller\auth;

use App\Http\Controllers\Controller;
use App\Models\Seller;
use App\Models\SellerWallet;
use App\Utils\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'identifier' => 'required',  // This will be either email or phone
            'password' => 'required|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $identifier = $request->identifier;
        $isEmail = filter_var($identifier, FILTER_VALIDATE_EMAIL);  // Check if the identifier is an email
        $field = $isEmail ? 'email' : 'phone';  // Use 'email' or 'phone' depending on the identifier

        // Query the Seller model based on either email or phone
        $seller = Seller::where($field, $identifier)->first();

        if (isset($seller) && $seller['status'] == 'approved') {
            $data = [
                $field => $identifier,  // Use the correct field
                'password' => $request->password
            ];

            // Attempt to log in with the provided credentials
            if (auth('seller')->attempt($data)) {
                $token = Str::random(50);  // Generate a new auth token
                Seller::where(['id' => auth('seller')->id()])->update(['auth_token' => $token]);

                // Check if seller has a wallet, create one if it doesn't exist
                if (SellerWallet::where('seller_id', $seller['id'])->first() == false) {
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
                }

                // Return the authentication token
                return response()->json(['token' => $token], 200);
            }
        }

        // If login fails or the account is not approved
        $errors = [];
        array_push($errors, ['code' => 'auth-001', 'message' => translate('Invalid credential or account not verified yet')]);
        return response()->json([
            'errors' => $errors
        ], 401);
    }
}
